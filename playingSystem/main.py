import cgi
from ctypes.wintypes import BYTE
from http.server import BaseHTTPRequestHandler, HTTPServer
from turtle import pos
import requests
from requests.structures import CaseInsensitiveDict
from urllib.parse import parse_qs
import playsound
import serial
import threading
import json


TOUCH_PINS = [32, 33, 14, 12, 13, 4, 0, 15]
NUMBER_OF_BYTES = 8
CHANGE_OCTAVE_BYTE_VALUE = 9
postQueue = []
loadedLibrarySoundsPath = []
currentOctave = 5
currentLibrary = "guitar"
libraries = {}
with open("libraries.json") as librariesJson:
    libraries = json.loads(librariesJson.read())

try:
    serialPort = serial.Serial(
        port="COM3", baudrate=9600, bytesize=8, timeout=2, stopbits=serial.STOPBITS_ONE)
except:
    print("Impossibile aprire la porta seriale COM3")
    exit(1)


def changeOctave():
    global currentOctave
    if (currentOctave + 1 in libraries[currentLibrary]["octave"]):
        currentOctave = currentOctave + 1
        loadLibrary(currentLibrary, currentOctave)
    else:
        currentOctave = libraries[currentLibrary]["octave"][0]
        loadLibrary(currentLibrary, currentOctave)


def loadLibrary(libraryName, octave):
    global currentLibrary
    global currentOctave
    currentLibrary = libraryName
    currentOctave = octave
    loadedLibrarySoundsPath.clear()
    for i in range(0, NUMBER_OF_BYTES):
        if (i == NUMBER_OF_BYTES - 1):
            if octave is not None:
                octave += 1
        loadedLibrarySoundsPath.append(libraryName + "/" + libraries[libraryName]["notes"][i] + (
            str(octave) if (octave is not None) else "") + ".wav")


def secondLoop():
    loading = False
    while True:
        if len(postQueue) > 0 and loading is not True:
            byte = postQueue.pop()
            loading = True
            try:
                headers = CaseInsensitiveDict()
                headers["Connection"] = "keep-alive"
                headers["Keep-Alive"] = "timeout=5, max=100"
                r = requests.post("http://localhost:80/esp32piano/server.php",
                                  headers=headers, data={"pin": TOUCH_PINS[byte]})
                print("HTTP RESPONSE STATUS CODE: " + str(r.status_code))
                loading = False
            except Exception as e:
                print(e)


def mainLoop():
    while True:
        if serialPort.in_waiting > 0:
            byte = int.from_bytes(serialPort.read(1), "big")
            if byte not in range(0, NUMBER_OF_BYTES+1):
                pass
            print(byte)
            postQueue.append(byte)
            if byte == CHANGE_OCTAVE_BYTE_VALUE:
                changeOctave()
            else:
                print("byte received: " + str(byte) + " current instrument: " + currentLibrary + " path: " +
                      loadedLibrarySoundsPath[byte] + " octave: " + (str(currentOctave) if not None else "nessuna ottava"))
                try:
                    playsound.playsound(loadedLibrarySoundsPath[byte], False)
                except:
                    print("non riesco a riprodurre il suono")


def webServerLoop():
    try:
        webServer.serve_forever()
    except KeyboardInterrupt:
        pass


hostName = "localhost"
serverPort = 5050


class MyServer(BaseHTTPRequestHandler):
    def do_GET(self):
        self.send_response(200)
        self.send_header("Content-type", "text/html")
        self.end_headers()
        self.wfile.write(
            bytes("<html><head><title>https://pythonbasics.org</title></head>", "utf-8"))
        self.wfile.write(bytes("<p>Request: %s</p>" % self.path, "utf-8"))
        self.wfile.write(bytes("<body>", "utf-8"))
        self.wfile.write(
            bytes("<p>This is an example web server.</p>", "utf-8"))
        self.wfile.write(bytes("</body></html>", "utf-8"))

    def do_POST(self):
        ctype, pdict = cgi.parse_header(self.headers["content-type"])
        if ctype == 'multipart/form-data':
            pdict["boundary"] = bytes(pdict["boundary"], "utf-8")
            postvars = cgi.parse_multipart(self.rfile, pdict)
        elif ctype == 'application/x-www-form-urlencoded':
            length = int(self.headers.get('content-length'))
            postvars = parse_qs(self.rfile.read(length).decode('utf-8'))

        if "library" in postvars:

            if postvars["library"][0] in libraries:
                if libraries[postvars["library"][0]]["octave"] is not None:
                    loadLibrary(
                        postvars["library"][0], libraries[postvars["library"][0]]["octave"][0])
                else:
                    loadLibrary(postvars["library"][0], None)
                self.send_response(200)
                self.send_header('Content-type', 'text/html')
                self.end_headers()
                output = ''
                output += '<html><body>'
                output += '<h1>Libreria cambiata</h1>'
                output += '</html></body>'
                self.wfile.write(output.encode('utf-8'))
            else:
                self.send_response(400)
                self.send_header('Content-type', 'text/html')
                self.end_headers()
                output = ''
                output += '<html><body>'
                output += '<h1>Errore</h1>'
                output += '</html></body>'
                self.wfile.write(output.encode('utf-8'))
        else:
            self.send_response(400)
            self.send_header('Content-type', 'text/html')
            self.end_headers()
            output = ''
            output += '<html><body>'
            output += '<h1>Errore nel payload</h1>'
            output += '</html></body>'
            self.wfile.write(output.encode('utf-8'))


webServer = HTTPServer((hostName, serverPort), MyServer)
print("Server started http://%s:%s" % (hostName, serverPort))
loadLibrary(currentLibrary, currentOctave)

t1 = threading.Thread(target=mainLoop)
t1.start()
t2 = threading.Thread(target=secondLoop)
t2.start()
try:
    webServer.serve_forever()
except KeyboardInterrupt:
    pass
webServer.server_close()
try:
    serialPort.close()
except:
    print("Non sono riuscito a chiudere la seriale.")
print("Server stopped.")
t1.join()
t2.join()
