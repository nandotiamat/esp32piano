#define NUMBER_OF_TOUCH_PINS 8
#include <WiFi.h>
#include <HTTPClient.h>
\
const char *ssid     = "iPhone di Emilio"; //Enter the router name
const char *password = "emiliotirozzi"; //Enter the router password

const char* post_link = "http://172.20.10.8:80/esp32piano/server.php";

WiFiClient client;

// const int pushButton = 21;


const int touchPins[NUMBER_OF_TOUCH_PINS] = {32, 33, 12, 14, 13, 4, 15, 0};
const int noteCodes[NUMBER_OF_TOUCH_PINS] = {90, 91, 92, 93, 94, 95, 96, 97}; // sono i bytes associati al tocco di ciascun pin
int touchValues[NUMBER_OF_TOUCH_PINS]; // array di appoggio per salvare i valori letti dai pin ad ogni iterazione
int pinsCounter[NUMBER_OF_TOUCH_PINS] = {0, 0, 0, 0, 0, 0, 0, 0};
bool pinFlags[NUMBER_OF_TOUCH_PINS] = {false, false, false, false, false, false, false};


// change with your threshold value
const int threshold = 20;

void setup() {


  Serial.begin(9600);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) { //Check for the connection
    delay(5000);
    Serial.println("Connecting to WiFi..");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());

  
}

void send_to_server(String postData) {
  HTTPClient http;    //Declare object of class HTTPClient
  if (WiFi.status() == WL_CONNECTED)
  { //Check WiFi connection status
    // Send and get Data
    http.begin(post_link);              //Specify request destination
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header
    int httpCode = http.POST(postData);   //Send the request
    //Serial.println(httpCode);   //Print HTTP return code
    http.end();  //Close connection
    Serial.println(postData);
  }

  else
  {
    Serial.println("Error in WiFi connection");
  }

}

void loop() {


  for (int j = 0; j < NUMBER_OF_TOUCH_PINS; j++) {

    touchValues[j] = touchRead(touchPins[j]);

    if (touchValues[j] < threshold) {
      pinsCounter[j]++;
      if (pinsCounter[j] > 3 && !pinFlags[j]) {
        pinFlags[j] = true;
        Serial.write(noteCodes[j]);
        send_to_server("pin = " + String(touchPins[j]));
      }
    }
    else {
      pinFlags[j] = false;
      pinsCounter[j] = 0;
    }
  }
  delay(10);
}
