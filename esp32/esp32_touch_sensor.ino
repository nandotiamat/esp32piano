 #define NUMBER_OF_TOUCH_PINS 5
 
 // set pin numbers
const int CtouchPin = 4; 
const byte CNoteCode = 97;

const int touchPins[NUMBER_OF_TOUCH_PINS] = {4, 32, 0, 33, 27};
const int noteCodes[NUMBER_OF_TOUCH_PINS] = {99, 97, 95, 93, 91};
int touchValues[NUMBER_OF_TOUCH_PINS];
int pinsCounter[NUMBER_OF_TOUCH_PINS] = {0, 0, 0, 0, 0};
bool pinFlags[NUMBER_OF_TOUCH_PINS] = {false, false, false, false, false};
const int EtouchPin = 2;
const byte ENoteCode = 99;

const int ledPin = 13;
bool flag;

// change with your threshold value
const int threshold = 20;
// variable for storing the touch pin value 
int touchValue;
int i;

void setup(){
  i = 0;
  flag = false;
  Serial.begin(9600);
  delay(1000); // give me time to bring up serial monitor
  // initialize the LED pin as an output:
  pinMode (ledPin, OUTPUT);
}

void loop(){

  for (int j = 0; j < NUMBER_OF_TOUCH_PINS; j++) {
    
    touchValues[j] = touchRead(touchPins[j]);
    // Serial.print("Pin ");
    // Serial.print(touchPins[j]);
    // Serial.print(" : ");
    // Serial.print(touchValues[j]); 
    
    if(touchValues[j] < threshold) {
      pinsCounter[j]++;
      if (pinsCounter[j] > 3 && !pinFlags[j]) {
        pinFlags[j] = true;
        // turn LED on
        digitalWrite(ledPin, HIGH);
        // Serial.println(" - LED on");
        Serial.write(noteCodes[j]);  
      }
    }
    else {
      pinFlags[j] = false;
      pinsCounter[j] = 0;
      // turn LED off
      digitalWrite(ledPin, LOW);
      // Serial.println(" - LED off");
    }
  }
  delay(20);
}
