//-----------------INCLUDES--------------------
#include <SPI.h>
#include <Ethernet.h>
#include <Base64.h>

#define MAX_CMD_LENGTH 5

byte mac[] = {
0xDE, 0xAA, 0xBB, 0xEF, 0x44, 0x55 };

IPAddress ip(192, 168, 1, 222);
IPAddress subnet(255, 255, 255, 0);
IPAddress dnss(192 , 168, 1, 1);
IPAddress gateway(192 ,168 ,1 , 1);

char zabbix[] = "xxxxx.no-ip.org" ; // youre zabbix server or you can just use IPAddress zabbix ( 192 , 168 , 1 ,xxx) your're zabbix server


IPAddress smartplug1(192 , 168 , 1 , 107); //smartplug1



EthernetClient Plug ;
EthernetClient zabclient ;






void setup()
{
Serial.begin(115200);
Serial.println("Starting connection");

// if you want to use dhcp

Ethernet.begin(mac);
delay(2000);
if (Ethernet.begin(mac) == 0 )
{
for(;;);
}


// or if you do not want to use dhcp then do not use the code above and use following line

//Ethernet.begin(mac, ip, dnss , gateway, subnet);

delay(1000);
// just printing config to serial
Serial.println(Ethernet.localIP()) ;
Serial.println(Ethernet.subnetMask());
Serial.println(Ethernet.gatewayIP());
Serial.println(Ethernet.dnsServerIP());
Serial.println("Ok, now we go ....");
Serial.println("Voltage divide by 1000 , Watt divide by 100, Cumul Watt divide by 1000");
}

void loop()
{
if(Plug.connect(smartplug1,23)) // making telnet connetion to plug
{
// Serial.println("Connected");
delay(200);
Plug.println("admin"); // user admin
delay(200);
Plug.println("admin"); // password admin
delay(1000);
Plug.flush();
GetPlugData("GetInfo V" ,"SmartPlug1","V1"); // VOLTAGE
GetPlugData("GetInfo W", "SmartPlug1","W1"); // WATTAGE
GetPlugData("GetInfo E","SmartPlug1","KW1"); // CUMUL WATTAGE

// you can also use GetInfo I , for the amps

Plug.flush();
Plug.stop();
delay(20000);


}
}

void GetPlugData(String command , String zhost , String zkey)
{
boolean dollar = false ;
Plug.flush();
Plug.println(command);
delay(1000);
command = "";
while (Plug.available())
{
char c = Plug.read() ;
if ( c == 36 ) dollar = true ;
if (dollar) command = command + c ;
//Serial.print(c); // for debug
}
command = command.substring(7,13);

// only for debug
//Serial.print(zhost) ;
// Serial.print(" ");
// Serial.print(zkey);
// Serial.print(" ");
// Serial.println(command);





if (dollar) SendZabbix(zhost,zkey,command);

}

void SendZabbix(String zhost ,String zkey , String zvalue )
{

char host[50];
char key[50];
char value[50];

zhost.toCharArray(host,50);
zkey.toCharArray(key,50);
zvalue.toCharArray(value,50);

char base64key[200];
char base64value[200];
char base64host[200];

//Serial.println("Sending to Zabbix ");

if (zabclient.connect(zabbix,10051))
{
//Serial.println("Connected to zabbix server ");
base64_encode(base64host , host , sizeof(host)-1);
base64_encode(base64key , key , sizeof(key)-1);
base64_encode(base64value , value , sizeof(value)-1);
zabclient.write("<req>\n");
zabclient.write(" <host>");
zabclient.write(base64host);
zabclient.write("</host>\n");
zabclient.write(" <key>");
zabclient.print(base64key);
zabclient.write("</key>\n");
zabclient.write(" <data>");
zabclient.write(base64value);
zabclient.write("</data>\n");
zabclient.write("</req>\n");
delay(5);
zabclient.stop();
}
else
Serial.println("no connection to zabbix");



}


/*

In zabbix :

Make a host SmartPlug1
With Items : KW1 , V1 , W1

Example W1

Host SmartPlug1

Name WattPlug1

Type Zabbix Trapper

Key W1

Type of information Numeric Float

Units

Use custom multiplier 0.01 for voltage 0.001 for kwh 0.001
*/