import cgi
from http.server import BaseHTTPRequestHandler, HTTPServer
from lib2to3.pgen2.token import NUMBER
from urllib.parse import parse_qs
import playsound
import serial
import threading
import json 

NUMBER_OF_BYTES = 8
loadedLibrarySoundsPath = []

libraries = {}
with open("libraries.json") as librariesJson:
    libraries = json.loads(librariesJson.read())
    
serialPort = serial.Serial(port = "COM4", baudrate=9600, bytesize=8, timeout=2, stopbits=serial.STOPBITS_ONE)

def loadLibrary(libraryName, octave):
    loadedLibrarySoundsPath.clear()
    octaveValue = octave
    for i in range(0,NUMBER_OF_BYTES):
        if (i == NUMBER_OF_BYTES - 1):
            octaveValue+=1
            print("octaveValue: " + str(octaveValue))
        loadedLibrarySoundsPath.append(libraryName + "/" + libraries[libraryName]["notes"][i] + str(octaveValue) + ".wav")

def mainLoop():
    while True:
        if serialPort.in_waiting > 0:
            byte = int(serialPort.read(1), 8)
            print(byte)
            playsound.playsound(loadedLibrarySoundsPath[byte], False)
    

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
        self.wfile.write(bytes("<html><head><title>https://pythonbasics.org</title></head>", "utf-8"))
        self.wfile.write(bytes("<p>Request: %s</p>" % self.path, "utf-8"))
        self.wfile.write(bytes("<body>", "utf-8"))
        self.wfile.write(bytes("<p>This is an example web server.</p>", "utf-8"))
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
                loadLibrary(postvars["library"][0])
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
loadLibrary("guitar", 5)

t1 = threading.Thread(target=mainLoop)
t1.start()
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
