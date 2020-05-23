bash "1_build.sh"
if [[ $? -ne 0 ]]; then
    echo "Error: Build failed"
    exit 1
fi

bash "2_start.sh"
if [[ $? -ne 0 ]]; then
    echo "Error: Start failed"
    exit 1
fi

bash "3_test.sh"
if [[ $? -ne 0 ]]; then
    echo "Error: Test failed"
    exit 1
fi

bash "4_stop.sh"
if [[ $? -ne 0 ]]; then
    echo "Error: Stop failed"
    exit 1
fi
