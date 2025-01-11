error_count="${#errors[@]}"
if [[ "${error_count}" -ge 1 ]]; then
    echo -e "\nErrors found: ${error_count}"

    iter=0
    for value in "${errors[@]}"; do
        ((iter++))
        echo -e "\nError ${iter} of ${error_count}:"
        echo "${value}" | perl -pe 's/^(.*)$/\t\1/'
    done
fi
