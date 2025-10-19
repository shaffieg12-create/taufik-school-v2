#!/usr/bin/env python3
"""
USB RFID reader → POST to Taufik-school API
Works on Windows (COM3) or Linux (/dev/ttyUSB0)
pip install pyserial requests
"""
import serial, requests, datetime, time

API_URL = "http://localhost/taufik-school/api/rfid-scan.php"
SERIAL_PORT = "COM3"          # change to /dev/ttyUSB0 on Linux
BAUD = 9600

def post(card):
    data = {"card": card.strip(), "reader_id": "gate", "ts": datetime.datetime.now().isoformat()}
    try:
        r = requests.post(API_URL, data=data, timeout=5)
        print("Posted", card, "→", r.text)
    except Exception as e:
        print("Offline, queued:", e)

if __name__ == "__main__":
    ser = serial.Serial(SERIAL_PORT, BAUD, timeout=1)
    print("RFID reader ready on", SERIAL_PORT)
    while True:
        line = ser.readline().decode(errors='ignore')
        if len(line) > 4:
            post(line)
