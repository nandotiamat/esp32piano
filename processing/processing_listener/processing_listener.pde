import processing.serial.*;
import ddf.minim.*;
import javax.sound.sampled.*;

AudioFormat format;
Serial myPort;
String portName;
Minim minim; 
int NUMBER_OF_BYTES;
AudioPlayer[] audioPlayers;

void setup()
{
  init();
}
 
void draw() {
  mainLoop();
}

void mainLoop() {
  while (myPort.available() > 0) {
    int inByte = myPort.read();
    switch (inByte) {
      case 90:
        audioPlayers[0].rewind();
        audioPlayers[0].play();
        break;
      case 91:
        audioPlayers[1].rewind();
        audioPlayers[1].play();
        break;      
      case 92:
        audioPlayers[2].rewind();
        audioPlayers[2].play();
      case 93:
        audioPlayers[3].rewind();
        audioPlayers[3].play();
        break;      
      case 94:
        audioPlayers[4].rewind();
        audioPlayers[4].play();
        break;
       case 95:
        audioPlayers[5].rewind();
        audioPlayers[5].play();
        break;
       case 96:
        audioPlayers[6].rewind();
        audioPlayers[6].play();
        break; 
       case 97:
       audioPlayers[7].rewind();
       audioPlayers[7].play();
       break;
    }
  }
}

void load_sound_library(AudioPlayer audioPlayers[]) {  
  audioPlayers[0] = minim.loadFile("notes/do.wav");
  audioPlayers[1] = minim.loadFile("notes/re.wav");
  audioPlayers[2] = minim.loadFile("notes/mi.wav");
  audioPlayers[3] = minim.loadFile("notes/fa.wav");
  audioPlayers[4] = minim.loadFile("notes/sol.wav");
  audioPlayers[5] = minim.loadFile("notes/la.wav");
  audioPlayers[6] = minim.loadFile("notes/si.wav");
  audioPlayers[7] = minim.loadFile("notes/do_octave.wav");
}

void init() {
  format  = AudioFormat(AudioFormat.Encoding.PCM_SIGNED, 44100.0, 16, 2, 4, 44100.0, false);
  portName = Serial.list()[0];
  myPort = new Serial(this, portName, 9600);
  NUMBER_OF_BYTES = 8;
  minim = new Minim(this);
  audioPlayers = new AudioPlayer[NUMBER_OF_BYTES];
  load_sound_library(audioPlayers);
}
