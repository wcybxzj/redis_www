uname_S:= $(shell sh -c 'uname -s 2>/dev/null || echo not')

CCCOLOR="\033[34m"
LINKCOLOR="\033[34;1m"
SRCCOLOR="\033[33m"
BINCOLOR="\033[37;1m"
MAKECOLOR="\033[32;1m"
ENDCOLOR="\033[0m"

default:
	@echo "Explicit target required"

.PHONY: default

# Prerequisites target
.make-prerequisites:
	@touch $@

distclean:
	(cd hiredis && $(MAKE) clean) > /dev/null || true
	(cd linenoise && $(MAKE) clean) > /dev/null || true
	(cd lua && $(MAKE) clean) > /dev/null || true
	(cd geohash-int && $(MAKE) clean) > /dev/null || true
	(cd jemalloc && [ -f Makefile ] && $(MAKE) distclean) > /dev/null || true
	(rm -f .make-*)


.PHONY: distclean

hiredis: .make-prerequisites
	@printf '%b %b\n' $(MAKECOLOR)MAKE$(ENDCOLOR) $(BINCOLOR)$@$(ENDCOLOR)
	cd hiredis && $(MAKE) static

.PHONY: hiredis


linenoise: .make-prerequisites
	@printf '%b %b\n' $(MAKECOLOR)MAKE$(ENDCOLOR) $(BINCOLOR)$@$(ENDCOLOR)
	cd linenoise && $(MAKE)

.PHONY: linenoise

AR=ar
ARFLAGS=rcu
