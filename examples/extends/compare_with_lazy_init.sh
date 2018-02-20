#!/usr/bin/env bash

SCRIPT_PATH="$(cd "$(dirname "$0")"; pwd -P)"
cd "${SCRIPT_PATH}"

count_times=(10 8 5 4 2)
count_requests=()
count_requests[10]=500
count_requests[8]=1000
count_requests[5]=2500
count_requests[4]=5000
count_requests[2]=10000

for i in ${count_times[*]}; do
    /usr/bin/time php run.php ${i} ${count_requests[i]}
    /usr/bin/time php run.php ${i} ${count_requests[i]} -e
done

