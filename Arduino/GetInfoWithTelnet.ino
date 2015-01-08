//-----------------INCLUDES--------------------
#include <SPI.h>
#include <Ethernet.h>

byte mac[] = {
0xDE, 0xAA, 0xBB, 0xEF, 0x44, 0x55 };

IPAddress ip(192, 168, 1, 222);
IPAddress subnet(255, 255, 255, 0);
IPAddress dnss(192 , 168, 1, 1);
IPAddress gateway(192 ,168 ,1 , 1);


IPAddress server(192 , 168 , 1 , 107); //smartplug

String Ivalue ;
String Wvalue ;
String Vvalue ;


EthernetClient Plug ;


void setup()
{
Serial.println("Starting connection");
Ethernet.begin(mac, ip, dnss , gateway, subnet);
Serial.begin(115200);
delay(1000);
// just printing config to serial
Serial.println(Ethernet.localIP()) ;
Serial.println(Ethernet.subnetMask());
Serial.println(Ethernet.gatewayIP());
Serial.println(Ethernet.dnsServerIP());
Serial.println("Ok, now we go ....");
Serial.println("Voltage divide by 1000 , Watt divide by 100");
}

void loop()
{
if(Plug.connect(server,23)) // making telnet connetion to plug
{
// Serial.println("Connected");
delay(200);
Plug.println("admin"); // user admin
delay(200);
Plug.println("admin"); // password admin
delay(1000);
Plug.flush();
Vvalue = GetPlugData("GetInfo V");
Wvalue = GetPlugData("GetInfo W");
Ivalue = GetPlugData("GetInfo I");
Plug.flush();
Plug.stop();
// Serial.println("Disconnecting");

Serial.print("Voltage : ");
Serial.print(Vvalue);
Serial.print(" Wattage : ");
Serial.println(Wvalue);



}
}

String GetPlugData(String command)
{
Plug.println(command);
delay(1000);
command = "";



while (Plug.available())
{
char c = Plug.read() ;
command = command + c ;
// Serial.print(c); // for debug
}

int firstdollarsign = command.indexOf('$');
// Serial.println(firstdollarsign) ; // debug
command = command.substring(firstdollarsign+7,firstdollarsign+13);



return (command);
}