# functions
die() { echo "$*" 1>&2 ; exit 1; }
confirm_yn(){
    while true; do
        read -n 1 -p "[Y]es / [N]o : " yn
        echo ""; # newline
        case $yn in
            [Yy]) return 0;;
            [Nn]) return 1;;
        esac
    done
}
isroot(){ [ $EUID == 0 ]; }
require_root() { isroot || die "Please run as root" }

# builtin aliases
# . source
# : true
#  https://www.gnu.org/software/bash/manual/html_node/Bash-Builtins.html
