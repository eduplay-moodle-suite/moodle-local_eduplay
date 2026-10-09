#!/usr/bin/env bash
# Contract test: the points of the third-party mod_interactivevideo that the adapter
# (amd/src/videourl.js and classes/hook_callbacks.php) depends on.
# Usage: bash tests/contract/check_interactivevideo.sh <path-to-mod_interactivevideo> [<path-to-this-plugin>]
set -uo pipefail

target="${1:?Usage: $0 <path-to-mod_interactivevideo> [<path-to-this-plugin>]}"
here="${2:-.}"
failures=0

check() {
  local description="$1"; shift
  if "$@" >/dev/null 2>&1; then
    echo "ok   - ${description}"
  else
    echo "FAIL - ${description}"
    failures=$((failures + 1))
  fi
}

version="$(awk -F"'" '/plugin->release/ {print $2; exit}' "${target}/version.php")"
echo "mod_interactivevideo release under test: ${version:-unknown}"

check "version.php declares mod_interactivevideo" grep -q "'mod_interactivevideo'" "${target}/version.php"
check "the add/edit form (mod_form.php) exists" test -f "${target}/mod_form.php"
check "the form has a text field named videourl" grep -Pzq "addElement\(\s*'text',\s*'videourl'" "${target}/mod_form.php"
check "the form validates videourl on the server" grep -q "errors\['videourl'\]" "${target}/mod_form.php"
check "the bulk import reads the videourl column on the server" grep -q "'videourl'" "${target}/lib.php"
check "the activity module is named interactivevideo (page type mod-interactivevideo-mod)" test -d "${target}/db"

# The adapter relies on the same names: keep both sides in step.
check "the adapter targets input[name=\"videourl\"]" grep -q 'input\[name="videourl"\]' "${here}/amd/src/videourl.js"
check "the hook limits the adapter to page type mod-interactivevideo-mod" grep -q "mod-interactivevideo-mod" "${here}/classes/hook_callbacks.php"

if [ "${failures}" -gt 0 ]; then
  echo "${failures} contract check(s) failed: mod_interactivevideo ${version} changed something the adapter depends on."
  exit 1
fi
echo "All contract checks passed for mod_interactivevideo ${version}"
