#!/usr/bin/env python3

# Reads a named pipe file and displays progress bars using curses.
#
# See multi_curl_progress_advanced.php
#
# $ ipython multi_curl_progress_advanced_watch_curses.py
#  56% [=====================>                  ]
#  23% [========>                               ]
#  15% [=====>                                  ]

import curses
import json
import os


FIFO = "/tmp/myfifo"


def main(stdscr):
    # Create named pipe file if it doesn't exist.
    if not os.path.exists(FIFO):
        os.mkfifo(FIFO)

    curses.noecho()
    curses.cbreak()

    try:
        while True:
            display_progress_bars(stdscr)
    except KeyboardInterrupt:
        # Handle Control-C pressed.
        pass
    finally:
        curses.echo()
        curses.nocbreak()
        curses.endwin()

    # Return exit code 0.
    return 0


def display_progress_bars(stdscr):
    display_notice = True

    while True:
        if display_notice:
            display_notice = False
            stdscr.clear()
            stdscr.addstr(0, 0, "waiting for input")
            stdscr.refresh()

        # Read named pipe file.
        with open(FIFO) as f:
            for line in f:
                response = json.loads(line)

                # Update progress for each of the files being downloaded.
                for entry in response.get("downloads", []):
                    # Display a progress bar: xxx% [=======>                                ]
                    progress_size = 40
                    try:
                        fraction_downloaded = entry["downloaded"] / entry["size"]
                    except ZeroDivisionError:
                        fraction_downloaded = 0
                    dots = round(fraction_downloaded * progress_size)
                    task_progress = "%3.0f%% [" % (fraction_downloaded * 100)

                    i = 0
                    while i < dots - 1:
                        task_progress += "="
                        i += 1

                    task_progress += ">"

                    while i < progress_size - 1:
                        task_progress += " "
                        i += 1

                    task_progress += "]"

                    stdscr.addstr(entry["position"], 0, task_progress)

                # Refresh display of the progress bars.
                stdscr.refresh()

                # Exit only after progress bars have been updated to end with
                # each displaying 100%.
                if response.get("status", "") == "done":
                    return


if __name__ == "__main__":
    # Avoid getting the terminal in an unmanagable state by using the curses
    # wrapper. The wrapper restores the terminal to its previous state even when
    # there is an uncaught exception.
    curses.wrapper(main)
