#!/usr/bin/env python3

# Reads a named pipe file and displays progress bars using tqdm.
#
# See multi_curl_progress_advanced.php
#
# $ ipython multi_curl_progress_advanced_watch_tqdm.py
# php_manual_en.html.gz:  56%|████████████████████████                   | 2.99M/5.34M [00:02<00:02, 1.14MB/s]
# php_manual_en.tar.gz:  23%|██████████▎                                  | 2.37M/10.4M [00:02<00:08, 901kB/s]
# php_manual_en.chm:  15%|███████▏                                        | 2.06M/13.7M [00:02<00:14, 783kB/s]

import json
import os
import sys

from tqdm import tqdm

FIFO = "/tmp/myfifo"


def main():
    # Create named pipe file if it doesn't exist.
    if not os.path.exists(FIFO):
        os.mkfifo(FIFO)

    try:
        while True:
            display_progress_bars()
    except KeyboardInterrupt:
        # Handle Control-C pressed.
        pass

    # Return exit code 0.
    return 0


def display_progress_bars():
    display_notice = True
    progress_bars = {}

    while True:
        if display_notice:
            display_notice = False
            print("waiting for input")

        # Read named pipe file.
        with open(FIFO) as f:
            for line in f:
                response = json.loads(line)

                # Update progress for each of the files being downloaded.
                for entry in response.get("downloads", []):
                    # Get or create progress bar.
                    progress_bar = progress_bars.get(entry["position"])
                    if progress_bar is None:
                        progress_bar = tqdm(
                            desc=entry["filename"],
                            total=entry["size"],
                            position=entry["position"],
                            unit_scale=True,
                            unit_divisor=1000,
                            unit="B",
                        )
                        progress_bars[entry["position"]] = progress_bar

                    # Update download progress.
                    progress_bar.n = entry["downloaded"]

                    # Update progress bar's total size. Initial size may have
                    # been sent as 0 until content-length was determined.
                    progress_bar.total = entry["size"]

                    # Refresh display of the progress bar.
                    progress_bar.refresh()

                # Exit only after progress bars have been updated to end with
                # each displaying 100%.
                if response.get("status", "") == "done":
                    return


if __name__ == "__main__":
    sys.exit(main())
