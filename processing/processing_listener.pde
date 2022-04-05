import processing.serial.*;
import ddf.minim.*;
 
Serial myPort;
Minim minim;
AudioPlayer doPlayer;
AudioPlayer rePlayer;
AudioPlayer miPlayer;
AudioPlayer faPlayer;
AudioPlayer solPlayer;
byte doCode = 99;
byte miCode = 97;
byte reCode = 95;
 
void setup()
{
  // In the next line, you'll need to change this based on your USB port name
  myPort = new Serial(this, "COM3", 9600);
  minim = new Minim(this);
 
  // Put in the name of your sound file below, and make sure it is in the same directory
  doPlayer = minim.loadFile("notes/do.wav");
  rePlayer = minim.loadFile("notes/mi.wav");
  miPlayer = minim.loadFile("notes/re.wav");
  faPlayer = minim.loadFile("notes/fa.wav");
  solPlayer = minim.loadFile("notes/sol.wav");
}
 
void draw() {
  while (myPort.available() > 0) {
    int inByte = myPort.read();
    switch (inByte) {
      case 97:
        miPlayer.rewind();
        miPlayer.play();
        break;
      case 99:
        doPlayer.rewind();
        doPlayer.play();
        break;
      case 95:
        rePlayer.rewind();
        rePlayer.play();
        break;      
      case 93:
        faPlayer.rewind();
        faPlayer.play();
        break;      
      case 91:
        solPlayer.rewind();
        solPlayer.play();
        break;
    
    }
  }
}
