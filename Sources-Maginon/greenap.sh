setGreenAP()
{
        start=`nvram_get 2860 GreenAPStart1`
        end=`nvram_get 2860 GreenAPEnd1`
        action=`nvram_get 2860 GreenAPAction1`
        if [ "$action" = "WiFiOFF" ]; then
                echo "$start * * * greenap.sh txpower 0" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX25" ]; then
                echo "$start * * * greenap.sh txpower 25" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX50" ]; then
                echo "$start * * * greenap.sh txpower 50" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX75" ]; then
                echo "$start * * * greenap.sh txpower 75" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "ON" ]; then
                # Modified by Joy 2013-11-18
                echo "$start * * * GpioForCrond 1" >> /var/spool/cron/crontabs/admin
                echo "$end * * * GpioForCrond 0" >> /var/spool/cron/crontabs/admin
        fi
        start=`nvram_get 2860 GreenAPStart2`
        end=`nvram_get 2860 GreenAPEnd2`
        action=`nvram_get 2860 GreenAPAction2`
        if [ "$action" = "WiFiOFF" ]; then
                echo "$start * * * greenap.sh txpower 0" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX25" ]; then
                echo "$start * * * greenap.sh txpower 25" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX50" ]; then
                echo "$start * * * greenap.sh txpower 50" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX75" ]; then
                echo "$start * * * greenap.sh txpower 75" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "ON" ]; then
                # Modified by Joy 2013-11-18
                echo "$start * * * GpioForCrond 1" >> /var/spool/cron/crontabs/admin
                echo "$end * * * GpioForCrond 0" >> /var/spool/cron/crontabs/admin
        fi
        start=`nvram_get 2860 GreenAPStart3`
        end=`nvram_get 2860 GreenAPEnd3`
        action=`nvram_get 2860 GreenAPAction3`
        if [ "$action" = "WiFiOFF" ]; then
                echo "$start * * * greenap.sh txpower 0" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX25" ]; then
                echo "$start * * * greenap.sh txpower 25" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX50" ]; then
                echo "$start * * * greenap.sh txpower 50" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX75" ]; then
                echo "$start * * * greenap.sh txpower 75" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "ON" ]; then
                # Modified by Joy 2013-11-18
                echo "$start * * * GpioForCrond 1" >> /var/spool/cron/crontabs/admin
                echo "$end * * * GpioForCrond 0" >> /var/spool/cron/crontabs/admin
        fi
        start=`nvram_get 2860 GreenAPStart4`
        end=`nvram_get 2860 GreenAPEnd4`
        action=`nvram_get 2860 GreenAPAction4`
        if [ "$action" = "WiFiOFF" ]; then
                echo "$start * * * greenap.sh txpower 0" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX25" ]; then
                echo "$start * * * greenap.sh txpower 25" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX50" ]; then
                echo "$start * * * greenap.sh txpower 50" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "TX75" ]; then
                echo "$start * * * greenap.sh txpower 75" >> /var/spool/cron/crontabs/admin
                echo "$end * * * greenap.sh txpower normal" >> /var/spool/cron/crontabs/admin
        elif [ "$action" = "ON" ]; then
                # Modified by Joy 2013-11-18
                echo "$start * * * GpioForCrond 1" >> /var/spool/cron/crontabs/admin
                echo "$end * * * GpioForCrond 0" >> /var/spool/cron/crontabs/admin
        fi
}

case $1 in
        "init")
                killall -q crond
                mkdir -p /var/spool/cron/crontabs
                rm -f /var/spool/cron/crontabs/admin
                cronebl="0"
                action=`nvram_get 2860 GreenAPAction1`
                if [ "$action" != "Disable" -a "$action" != "" ]; then
                        # Marked by Joy 2013-11-15
                        #start=`nvram_get 2860 GreenAPStart1`
                        cronebl="1"
                        # Marked by Joy 2013-11-15
                        #greenap.sh setchk $start
                fi
                action=`nvram_get 2860 GreenAPAction2`
                if [ "$action" != "Disable" -a "$action" != "" ]; then
                        # Marked by Joy 2013-11-15
                        #start=`nvram_get 2860 GreenAPStart2`
                        cronebl="1"
                        # Marked by Joy 2013-11-15
                        #greenap.sh setchk $start
                fi
                action=`nvram_get 2860 GreenAPAction3`
                if [ "$action" != "Disable" -a "$action" != "" ]; then
                        # Marked by Joy 2013-11-15
                        #start=`nvram_get 2860 GreenAPStart3`
                        cronebl="1"
                        # Marked by Joy 2013-11-15
                        #greenap.sh setchk $start
                fi
                action=`nvram_get 2860 GreenAPAction4`
                if [ "$action" != "Disable" -a "$action" != "" ]; then
                        # Marked by Joy 2013-11-15
                        #start=`nvram_get 2860 GreenAPStart4`
                        cronebl="1"
                        # Marked by Joy 2013-11-15
                        #greenap.sh setchk $start
                fi
                if [ "$cronebl" = "1" ]; then
                        # Added by Joy 2013-11-15
                        setGreenAP

                        crond
                fi
                ;;
        "setchk")
                if [ "$2" -lt "1" ]; then
                        if [ "$3" -lt "1" ]; then
                                hour=23
                        else
                                hour=`expr $3 - 1`
                        fi
                        minute=`expr 60 + $2 - 1`
                else
                        hour=$3
                        minute=`expr $2 - 1`
                fi
                echo "$minute $hour * * * greenap.sh chkntp" >> /var/spool/cron/crontabs/admin
                ;;
        "chkntp")
                # Modified by Joy 2013-11-15
                #cat /var/spool/cron/crontabs/admin | sed '/ifconfig/d' > /var/spool/cron/crontabs/admin
                #cat /var/spool/cron/crontabs/admin | sed '/txpower/d' > /var/spool/cron/crontabs/admin
                mkdir -p /var/spool/cron/crontabs
                rm -f /var/spool/cron/crontabs/admin

                index=1
                while [ "$index" -le 10 ]
                do
                        ntpvalid=`nvram_get 2860 NTPValid`
                        if [ "$ntpvalid" = "1" ]; then
                                setGreenAP
                                break;
                        else
                                # Modified by Joy 2013-11-14
                                #index=`expr $index + 1`
                                #sleep 5
                                setGreenAP
                                break;
                        fi
                done
                killall -q crond
                crond
                ;;
        "txpower")
                if [ "$2" = "normal" ]; then
                        ralink_init gen 2860
                        BssidNum=`nvram_get 2860 BssidNum`
                        num=$BssidNum
                        while [ "$num" -gt 0 ]
                        do
                                num=`expr $num - 1`
                                ifconfig ra$num down
                        done
                        while [ $num -lt $bssidnum ]
                        do
                                ifconfig ra$num up
                                num=`expr $num + 1`
                        done
                elif [ $2 = "0" ]; then
                        BssidNum=`nvram_get 2860 BssidNum`
                        num=$BssidNum
                        while [ "$num" -gt 0 ]
                        do
                                num=`expr $num - 1`
                                ifconfig ra$num down
                        done
                        while [ $num -lt $Bssidnum ]
                        do
                                ifconfig ra$num up
                                num=`expr $num + 1`
                        done
                else
                        cat /etc/Wireless/RT2860/RT2860.dat | sed '/TxPower/d' > /etc/Wireless/RT2860/RT2860.dat
                        txpw=$2
                        BssidNum=`nvram_get 2860 BssidNum`
                        num=1
                        while [ $num -lt $Bssidnum ]
                        do
                                txpw="$txpw;$2"
                                num=`expr $num + 1`
                        done
                        echo "TxPower=$txpw" >> /etc/Wireless/RT2860/RT2860.dat
                        while [ "$num" -gt 0 ]
                        do
                                num=`expr $num - 1`
                                ifconfig ra$num down
                        done
                        while [ $num -lt $Bssidnum ]
                        do
                                ifconfig ra$num up
                                num=`expr $num + 1`
                        done
                fi
                ;;
esac
