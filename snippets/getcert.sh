#!/bin/bash
# exports the cert with chain from pfx or p12 to .pem for apache
# usage:
#   getcert cert.p12 > fullchain.pem
# apache conf:
#   SSLCertificateFile fullchain.pem
#   # SSLCertificateChainFile not needed
openssl pkcs12 -in $1 -nokeys
