import requests
import datetime
import json
import os
import smtplib
import sys
from requests.exceptions import Timeout
from email.mime.text import MIMEText
from email.utils import formatdate
from email.header import Header


# メール送信
def send_mail(title, text):
    monitor_dir = os.path.dirname(os.path.abspath(__file__))

    readfile = open(monitor_dir + '/config.json', 'r')
    json_file = json.load(readfile)
    from_address = json_file['mail']['address']
    to_address = json_file['mail']['to']
    port = json_file['mail']['port']
    smtp_server = json_file['mail']['smtp']

    charset = 'ISO-2022-JP'
    msg = MIMEText(text, 'plain', charset)
    msg['Subject'] = Header(title, charset)
    msg['From'] = from_address
    msg['To'] = to_address
    msg['Date'] = formatdate(localtime=True)

    smtp = smtplib.SMTP(smtp_server, port)
    smtp.sendmail(from_address, to_address, msg.as_string())
    smtp.close()


def get_resource(address, port):
    pass


# データファイルの書き込み
def write_data_file(text, d, data_dir, debug=0):
    filename = '{0}/http_response-{1}-{2}-{3}.txt'.format(data_dir, d.year, str(d.month).zfill(2), str(d.day).zfill(2))

    if not debug:
        with open(filename, mode='a') as f:
            f.write(text)


def get_http_response(debug):
    d = datetime.datetime.today()
    monitor_dir = os.path.dirname(os.path.abspath(__file__))

    readfile = open(monitor_dir + '/config.json', 'r')
    json_file = json.load(readfile)
    data_dir = "/opt/monitor/data"
    url = json_file['server']['url']
    status_flag = 0

    try:
        r = requests.get(url, timeout=10)
        response_time = r.elapsed.total_seconds()
        status = r.status_code
        status_flag = 0
        text = '{0} {1} {2}\n'.format(d.isoformat(' '), status, response_time)
    except:
        status_flag = 1
        text = '{0} 0 0\n'.format(d.isoformat(' '))

    if not debug:
        write_data_file(text, d, data_dir)
    else:
        print(text)

    # サーバがタイムアウトになった場合、メール通知
    # if statusflag == 1:
    #    send_mail("alart: timeout", "server response timeout")


if __name__ == '__main__':
    if sys.argv[1:2]:
        if sys.argv[1] == "debug":
            get_http_response(True)
        else:
            print("usage: python3 monitor.py [debug]")
    else:
        get_http_response(False)
