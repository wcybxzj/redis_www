#include "server.h"

int getTimeoutFromObjectOrReply(client *c, robj *object, mstime_t *timeout, int unit) {
    long long tval;

    if (getLongLongFromObjectOrReply(c,object,&tval,
        "timeout is not an integer or out of range") != C_OK)
        return C_ERR;

    if (tval < 0) {
        addReplyError(c,"timeout is negative");
        return C_ERR;
    }

    if (tval > 0) {
        if (unit == UNIT_SECONDS) tval *= 1000;
        tval += mstime();
    }
    *timeout = tval;

    return C_OK;
}

/* Block a client for the specific operation type. Once the CLIENT_BLOCKED
 * flag is set client query buffer is not longer processed, but accumulated,
 * and will be processed when the client is unblocked. */
void blockClient(client *c, int btype) {
    c->flags |= CLIENT_BLOCKED;
    c->btype = btype;
    server.bpop_blocked_clients++;
}

void unblockClient(client *c) {
    if (c->btype == BLOCKED_LIST) {
        unblockClientWaitingData(c);
    } else if (c->btype == BLOCKED_WAIT) {
        //unblockClientWaitingReplicas(c);
    } else {
        serverPanic("Unknown btype in unblockClient().");
    }
    c->flags &= ~CLIENT_BLOCKED;
    c->btype = BLOCKED_NONE;
    server.bpop_blocked_clients--;
    if (!(c->flags & CLIENT_UNBLOCKED)) {
        c->flags |= CLIENT_UNBLOCKED;
        listAddNodeTail(server.unblocked_clients,c);
    }
}

void disconnectAllBlockedClients(void) {
    listNode *ln;
    listIter li;

    listRewind(server.clients,&li);
    while((ln = listNext(&li))) {
        client *c = listNodeValue(ln);

        if (c->flags & CLIENT_BLOCKED) {
            addReplySds(c,sdsnew(
                "-UNBLOCKED force unblock from blocking operation, "
                "instance state changed (master -> slave?)\r\n"));
            unblockClient(c);
            c->flags |= CLIENT_CLOSE_AFTER_REPLY;
        }
    }
}

