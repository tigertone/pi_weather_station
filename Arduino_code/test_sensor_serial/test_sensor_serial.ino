
void setup() {
  Serial.begin(115200);
  pinMode(3, INPUT);
  pinMode(4, INPUT);
  pinMode(5, INPUT);
}

// the loop routine runs over and over again forever:
void loop() {
  int temp = analogRead(3);
  long humidity = analogRead(4);
  humidity = humidity *100 / int(1023);
  int pressure = analogRead(5)*3;

  // print out the state of the button:
  Serial.println("start");
  Serial.println(temp);
  Serial.println(humidity);
  Serial.println(pressure);
  delay(5000);    
}



