import processing.serial.*;
import ddf.minim.*;
 
Serial myPort;
String portName = Serial.list()[0];
Minim minim;
AudioPlayer doPlayer;
AudioPlayer rePlayer;
AudioPlayer miPlayer;
AudioPlayer faPlayer;
AudioPlayer solPlayer;
AudioPlayer laPlayer;
AudioPlayer siPlayer;
AudioPlayer do_octavePlayer;
byte doCode = 90;
byte reCode = 91;
byte miCode = 92;
byte faCode = 93;
byte solCode = 94;
byte laCode = 95;
byte siCode = 96;
byte do_octaveCode = 97;
 
void setup()
{
  // In the next line, you'll need to change this based on your USB port name
  myPort = new Serial(this, portName, 9600);
  minim = new Minim(this);
 
  // Put in the name of your sound file below, and make sure it is in the same directory
  doPlayer = minim.loadFile("notes/do.wav");
  rePlayer = minim.loadFile("notes/re.wav");
  miPlayer = minim.loadFile("notes/mi.wav");
  faPlayer = minim.loadFile("notes/fa.wav");
  solPlayer = minim.loadFile("notes/sol.wav");
  laPlayer = minim.loadFile("notes/la.wav");
  siPlayer = minim.loadFile("notes/si.wav");
  do_octavePlayer = minim.loadFile("notes/do_octave.wav");
}
 
void draw() {
  while (myPort.available() > 0) {
    int inByte = myPort.read();
    switch (inByte) {
   
      case 90:
        doPlayer.rewind();
        doPlayer.play();
        break;
      case 91:
        rePlayer.rewind();
        rePlayer.play();
        break;      
      case 92:
        miPlayer.rewind();
        miPlayer.play();
      case 93:
        faPlayer.rewind();
        faPlayer.play();
        break;      
      case 94:
        solPlayer.rewind();
        solPlayer.play();
        break;
       case 95:
        laPlayer.rewind();
        laPlayer.play();
        break;
       case 96:
        siPlayer.rewind();
        siPlayer.play();
        break; 
       case 97:
       do_octavePlayer.rewind();
       do_octavePlayer.play();
       break;
    
    }
  }
}
