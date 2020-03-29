#!/bin/bash
set -o errexit
set -o nounset
set -o pipefail

__self="${BASH_SOURCE[0]}"
__dir="$(cd "$(dirname "${__self}")" > /dev/null && pwd)"
__file="${__dir}/$(basename "${__self}")"

if [[ "$#" -ne 1 ]]; then
	echo "Usage: $0 <version>"
	exit 1
fi

version="$1"

cd "${__dir}/../js"
rm -rf dist
npm run build
cd ..

branch="$(git rev-parse --abbrev-ref HEAD)"
tmpbranch="tmp-tag-${version}"
message="Version ${version}"

git checkout -b "${tmpbranch}"
git add -f js/dist/*.js js/dist/*.js.map
git commit -a -m "${message}"
git tag -a "${version}" -m "${message}"
git checkout "${branch}"
git branch -D "${tmpbranch}"
