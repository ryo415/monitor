FROM centos:7

RUN yum -y install epel-release
RUN yum -y install python36 python36-libs python36-devel python36-pip
RUN pip3.6 install requests
RUN yum -y install crontabs
RUN yum -y install --enablerepo=epel supervisor
RUN mkdir /opt/monitor
RUN ln -sf  /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
ADD ./monitor.py /opt/monitor/
ADD ./config.json /opt/monitor/
ADD ./cron /etc/cron.d/monitor
ADD ./supervisor.conf /root/
RUN cat /root/supervisor.conf >> /etc/supervisord.conf

CMD ["/usr/bin/supervisord"]
