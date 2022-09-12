#!/usr/bin/env bash
#
# The core commands execute start from the "MAIN" section below.
#

test -z "$BASH_SOURCE" && {
    self="sudo -E bash"
    prefix="<curl command> |"
} || {
    self=$(readlink -f ${BASH_SOURCE:-$0})
    prefix=""
}

tmp_log=$(mktemp .csm_setup_XXXXXXXXX)

colours=$(tput colors 2>/dev/null || echo "256")
no_colour="\e[39;49m"
green_colour="\e[32m"
red_colour="\e[41;97m"
bold="\e[1m"
reset="\e[0m"
use_colours=$(test -n "$colours" && test $colours -ge 8 && echo "yes")
test "$use_colours" == "yes" || {
  no_colour=""
  green_colour=""
  red_colour=""
  bold=""
  reset=""
}


example_name="Alpine (v3.8)"
example_distro="alpine"
example_codename=""
example_version="3.8.0"


function echo_helptext {
    local help_text="$*"
    echo " ^^^^: ... $help_text"
}

function die {
    local text="$@"
    test ! -z "$text" && {
      echo_helptext "$text" 1>&2
    }

    local prefix="${red_colour} !!!!${no_colour}"

    echo -e "$prefix: Oh no, your setup failed! :-( ... But we might be able to help. :-)"
    echo -e "$prefix: "
    echo -e "$prefix: ${bold}You can contact Symfony for further assistance.${reset}"
    echo -e "$prefix: "
    
    echo -e "$prefix: ${bold}URL: https://github.com/symfony-cli/symfony-cli${reset}"
    echo -e "$prefix: "
    

    test -f "$tmp_log" && {
      local n=20
      echo -e "$prefix: Last $n log lines from $tmp_log (might not be errors, nor even relevant):"
      echo -e "$prefix:"
      check_tool_silent "xargs" && {
        check_tool_silent "fmt" && {
          tail -n $n $tmp_log | fmt -t | xargs -Ilog echo -e "$prefix: > log"
        } || {
          tail -n $n $tmp_log | xargs -Ilog echo -e "$prefix: > log"
        }
      } || {
        echo
        tail -n $n $tmp_log
      }
    }
    exit 1
}

function echo_colour {
    local colour="${1:-"no"}_colour"; shift
    echo -e "${!colour}$@${no_colour}"
}

function echo_green_or_red {
    local rc="$1"
    local good="${2:-YES}"
    local bad="${3:-NO}"

    test "$rc" -eq 0 && {
        echo_colour "green" "$good"
    } || {
        echo_colour "red" "$bad"
    }
    return $rc
}

function echo_clearline {
    local rc="$?"
    echo -e -n "\033[1K\r"
    return $rc
}

function echo_status {
    local rc="$1"
    local good="$2"
    local bad="$3"
    local text="$4"
    local help_text="$5"
    local newline=$(test "$6" != "no" && echo "\n" || echo "")
    local status_text=$(echo_green_or_red "$rc" "$good" "$bad")

    echo_clearline
    local width=$(test "$use_colours" == "yes" && echo "16" || echo "5")
    printf "%${width}s %s${newline}" "${status_text}:" "$text"
    test $rc -ne 0 && test ! -z "$help_text" && {
        echo_helptext "$help_text"
        echo
    }

    return $rc
}

function echo_running {
    local rc=$?
    local text="$1"
    echo_status 0 "  RUN" " RUN" "$text" "" "no"
    return $rc
}

function echo_okfail_rc {
    local rc=$1
    local text="$2"
    local help_text="$3"
    echo_clearline
    echo_status $rc "   OK" " NOPE" "$text" "$help_text"
    return $rc
}

function echo_okfail {
    echo_okfail_rc $? "$@"
    return $?
}

function check_tool_silent {
    local tool=${1}
    command -v $tool &>/dev/null || which $tool &>/dev/null
    return $?
}

function check_tool {
    local tool=${1}
    local optional=${2:-false}
    local required_text="optional"
    if ! $optional; then required_text="required"; fi
    local text="Checking for $required_text executable '$tool' ..."
    echo_running "$text"
    check_tool_silent "$tool"
    echo_okfail "$text" || {
        if ! $optional; then
            die "$tool is not installed, but is required by this script."
        fi
        return 1
    }
    return 0
}

function cleanup {
    echo


    rm -rf $tmp_log
}

function shutdown {
    echo_colour "red" " !!!!: Operation cancelled by user!"
    exit 2
}



function check_os {
    test ! -z "$distro" && test ! -z "${version}${codename}"
    return $?
}

function detect_os_system {
    check_os && return 0
    echo_running "$text"
    local text="Detecting your OS distribution and release using system methods ..."

    local tool_rc=1
    test -f '/etc/os-release' && {
      . /etc/os-release
      distro=${distro:-$ID}
      codename=${codename:-$VERSION_CODENAME}
      codename=${codename:-$(echo $VERSION | cut -d '(' -f 2 | cut -d ')' -f 1)}
      version=${version:-$VERSION_ID}

      test -z "${version}${codename}" && test -f '/etc/debian_version' && {
        # Workaround for Debian unstable releases; get the codename from debian_version
        codename=$(cat /etc/debian_version | cut -d '/' -f1)
      }

      tool_rc=0
    }

    check_os
    local rc=$?
    echo_okfail_rc $rc "$text"

    test $tool_rc -eq 0 && {
      report_os_expanded
    }

    return $rc
}

function report_os_attribute {
  local name=$1
  local value=$2
  local coloured=""
  echo -n "$name="
  test -z "$value" && {
    echo -e -n "${red_colour}<empty>${no_colour}  "
  } || {
    echo -e -n "${green_colour}${value}${no_colour}  "
  }
}

function report_os_expanded {
  echo_helptext "Detected/provided for your OS/distribution, version and architecture:"
  echo " >>>>:"
  report_os_values
}

function report_os_values {
  echo -n " >>>>: ... "
  report_os_attribute "distro" $distro
  report_os_attribute "version" $version
  report_os_attribute "codename" $codename
  report_os_attribute "arch" $arch
  echo
  echo " >>>>:"
}

function detect_os_legacy_python {
    check_os && return 0

    local text="Detecting your OS distribution and release using legacy python ..."
    echo_running "$text"

    IFS='' read -r -d '' script <<-'EOF'
from __future__ import unicode_literals, print_function
import platform;
info = platform.linux_distribution() or ('', '', '');
for key, value in zip(('distro', 'version', 'codename'), info):
    print("local guess_%s=\"%s\"\n" % (key, value.lower().replace(' ', '')));
EOF

    local tool_rc=1
    check_tool_silent "python" && {
      eval $(python -c "$script")
      distro=${distro:-$guess_distro}
      codename=${codename:-$guess_codename}
      version=${version:-$guess_version}
      tool_rc=$?
    }

    check_os
    local rc=$?
    echo_okfail_rc $rc "$text"

    check_tool_silent "python" || {
      echo_helptext "Python isn't available, so skipping detection method (hint: install python)"
    }

    test $tool_rc -eq 0 && {
      report_os
    }

    return $rc
}

function detect_os_modern_python {
    check_os && return 0

    check_tool_silent "python" && {
      local text="Ensuring python-pip is installed ..."
      echo_running "$text"
      check_tool_silent "pip"
      echo_okfail "$text" || {
          local text="Checking if pip can be bootstrapped without get-pip ..."
          echo_running "$text"
          python -m ensurepip --default-pip &>$tmp_log
          echo_okfail "$text" || {
              local text="Installing pip via get-pip bootstrap ..."
              echo_running "$text"
              curl -1sLf https://bootstrap.pypa.io/get-pip.py 2>$tmp/log | python &>$tmp_log
              echo_okfail "$text" || die "Failed to install pip!"
          }
      }

      # FIXME(ls): Install distro into a temporary virtualenv
      local text="Installing 'distro' python library ..."
      echo_running "$text"
      python -c 'import distro' &>$tmp_log || python -m pip install distro &>$tmp_log
      echo_okfail "$text" || die "Failed to install required 'distro' python library!"
    }

    IFS='' read -r -d '' script <<-'EOF'
from __future__ import unicode_literals, print_function
import distro;
info = distro.linux_distribution(full_distribution_name=False) or ('', '', '');
for key, value in zip(('distro', 'version', 'codename'), info):
    print("local guess_%s=\"%s\"\n" % (key, value.lower().replace(' ', '')));
EOF

    local text="Detecting your OS distribution and release using modern python ..."
    echo_running "$text"

    local tool_rc=1
    check_tool_silent "python" && {
      eval $(python -c "$script")
      distro=${distro:-$guess_distro}
      codename=${codename:-$guess_codename}
      version=${version:-$guess_version}
      tool_rc=$?
    }

    check_os
    local rc=$?
    echo_okfail_rc $rc "$text"

    check_tool_silent "python" || {
      echo_helptext "Python isn't available, so skipping detection method (hint: install python)"
    }

    test $tool_rc -eq 0 && {
      report_os_expanded
    }

    return $rc
}

function detect_os {
    # Backwards compat for old distribution parameter names
    distro=${distro:-$os}
    codename=${codename:-$dist}

    arch=${arch:-$(arch || uname -m)}

    detect_os_system ||
      detect_os_legacy_python ||
      detect_os_modern_python

    (test -z "$distro" || test -z "${version}${codename}") && {
      echo_okfail_rc "1" "Unable to detect your OS distribution and/or release!"
      cat <<EOF
 >>>>:
 >>>>: The 'distro' value is required, and either 'version' or 'codename' values,
 >>>>: or both. Without these, the install script cannot retrieve the correct
 >>>>: configuration for this system.
 >>>>:
 >>>>: You can force this script to use a particular value by specifying distro,
 >>>>: version, or codename via environment variable. E.g., to specify a distro
 >>>>: such as $example_name, use the following:
 >>>>:
 >>>>: $prefix distro=$example_distro version=$example_version codename=$example_codename $self
 >>>>:
EOF
      die
    }
}


function config_url {
    echo "https://dl.cloudsmith.io/public/symfony/stable/config.alpine.txt?distro=${distro}&codename=${codename}&version=${version}&arch=${arch}" | sed 's/ /%20/g'
}

function check_fetch_config {
    local text="Checking if upstream install config is OK ..."
    echo_running "$text"
    local code="$(curl -1IsL -w "%{http_code}\\n" "$(config_url)" -o /dev/null --connect-timeout 15 --max-time 60)"
    test "$code" == "200" && {
      echo_okfail_rc 0 "$text"
      return 0
    } || {
        echo_okfail_rc 1 "$text"
        echo_helptext "Failed to fetch configuration for your OS distribution release/version."
        cat <<EOF
 >>>>:
EOF
        test "$code" == "404" && {
          cat <<EOF
 >>>>: It looks like we don't currently support your distribution release and
 >>>>: version. This is something that we can fix by adding it to our list of
 >>>>: supported versions (see contact us below), or you can manually override
 >>>>: the values below to an equivalent distribution that we do support:
 >>>>:
EOF
          report_os_values
        } || {
          cat <<EOF
 >>>>: It looks like it might be because the configuration endpoint is not
 >>>>: currently available. Please try again in 10-15 minutes. If it still
 >>>>: doesn't work after an hour, please contact Symfony
 >>>>: for assistance.
 >>>>:
EOF
        }

        cat <<EOF
 >>>>: You can force this script to use a particular value by specifying distro,
 >>>>: version, or codename via environment variable. E.g., to specify a distro
 >>>>: such as $example_name, use the following:
 >>>>:
 >>>>:   $prefix distro=$example_distro version=$example_version codename=$example_codename $self
 >>>>:
EOF
        die
    }
}

function fetch_config {
    curl -1sLf "$(config_url)"
}


function update_apk {
    local text="Updating alpine repository metadata cache ..."
    echo_running "$text"
    apk update &>$tmp_log
    echo_okfail "$text" ||
        die "Failed to update via apk update"
}

function import_rsa_key {
    local text="Importing 'symfony/stable' repository RSA key..."
    echo_running "$text"
    curl -1sLf "https://dl.cloudsmith.io/public/symfony/stable/rsa.1C204ECEF7BED6AB.key" > "/etc/apk/keys/stable@symfony-1C204ECEF7BED6AB.rsa.pub"
    echo_okfail "$text" || die "Could not import the RSA key for this repository"
}

function setup_repository {
    local repo_path="/etc/apk/repositories"
    check_fetch_config

    local text="Installing 'symfony/stable' repository..."
    echo_running "$text"
    fetch_config >> "$repo_path"
    echo_okfail "$text" || die "Could not install the repository, do you have permissions?"
}



function usage () {
     cat <<EOF
Usage: $self [opts]
  -h Displays this usage text.
  -i Ignore repository setup errors during setup and
     continue with install.  This will leave the repository config
     in place rather than removing it upon errors.
EOF
     exit 0
}


trap cleanup EXIT
trap shutdown INT


ignore_errors=1



while getopts ":ih" OPT; do
    case $OPT in
         i) ignore_errors=0 ;;
         h) usage ;;
        \?) usage ;;
    esac
done
shift $(($OPTIND - 1))


#
# MAIN
#

echo "Executing the  setup script for the 'symfony/stable' repository ..."
echo


check_tool "curl"



detect_os

import_rsa_key
setup_repository
update_apk


echo_okfail_rc "0" "The repository has been installed successfully - You're ready to rock!"
