warning_count="${#warnings[@]}"
if [[ "${warning_count}" -ge 1 ]]; then
    echo -e "\nWarnings found: ${warning_count}"

    iter=0
    for value in "${warnings[@]}"; do
        ((iter++))
        echo -e "\nWarning ${iter} of ${warning_count}:"
        echo "⚠️ ${value}" | perl -pe 's/^(.*)$/\t\1/'
    done
fi
