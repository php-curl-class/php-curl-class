screen_name="my_screen"
server_count=5
# screen_binary="screen"
screen_binary="byobu-screen"

# Start screen in detached mode with a session name.
screen -S "${screen_name}" -t "master" -d -m

# Wait for screen to be ready before opening new sessions.
sleep 1

# Create tabs and send commands to each.
for i in $(seq 1 "${server_count}"); do
    # Create tab.
    screen -S "${screen_name}" -X "screen" -t "my_screen_${i}"

    # Start development server in tab.
    port=8000
    (( port += $i ))
    command="php -S 127.0.0.1:${port} -t PHPCurlClass/"
    screen -S "${screen_name}" -p "my_screen_${i}" -X stuff "${command}"$'\n'
done

screen -r "${screen_name}"
