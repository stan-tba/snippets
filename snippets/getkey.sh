#!/bin/bash
# extracts key from pfx or p12 file into un-passworded pem key for apache
# usage:
#   getkey.sh cert.p12 > key.pem
# just enter the same key every time (all 4 times)
# first command asks twice for new pem key which must be entered again at second command
# technically, the first password must be correct and the last three can be anything as long as they are the same
openssl pkcs12 -in $1 -nocerts | openssl rsa
