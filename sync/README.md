Aws autoscaling code deployer
=========

###Automatically deploy code changes from github repository across all EC2 instances behind AWS autoscaling group in a single region.

How it works
--
  - You push to github repository (public/private)
  - [Github webhook URL] will be invoked
  - **Aws autoscaling code deployer** will instantly deploy those changes
  - That's it. :P

>**For now I'm assuming the following parameters, please change those depending upon your requirements**
- Web server: Apache2
- Document root: ```/var/www```
- Apache username: ```www-data```
- General username: ```ubuntu```

**Important**

1. It'll work irrespective of the repository type, and I guess in most cases your github repository will be private although it's entirely upto you.

2. [Github webhook URL]: Use either one of Public DNS/Elastic IP of anyone of the EC2 instance behind autoscaling group or ELB A record or your domain name. Keep in mind that all of those must point to the location ```aws_autoscaling_code_deployer/index.php```

 **Public DNS:** http://ec2-xxx-xxx-xxx-xxx.region-name.compute.amazonaws.com/path/to/aws_autoscaling_code_deployer/index.php

 **Elastic IP:** http://xxx-xxx-xxx-xxx/path/to/aws_autoscaling_code_deployer/index.php

 **ELB A record:** http://elb_name-xxxxxxxxx.region-name.elb.amazonaws.com/path/to/aws_autoscaling_code_deployer/index.php

 **Domain name:** http://www.example.com/path/to/aws_autoscaling_code_deployer/index.php

Setting up your repository
--
Make sure that your repository contains ```index.php``` and ```config.php``` of this repository inside ```aws_autoscaling_code_deployer``` directory

1. Specify your AWS credentials and configs in ```config.php```
 - Access key ID

    **Example:**  ```AKIAIOSFODNN7EXAMPLE```
 - Secret access key: 
  
    **Example:** ```wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY```
 - Region: AWS region where you've setup your autoscaling group

    Must be set to one of the following values:
    > us-east-1, ap-northeast-1, sa-east-1, ap-southeast-1, ap-southeast-2, us-west-2, us-gov-west-1, us-west-1, cn-north-1, eu-west-1
 - Autoscaling group: Name of your autoscaling group
 - Document root: Your webserver document root

    **Example:** ```/var/www```

2. Setup a service hook such that it points to index.php of this repository by navigating to
your **github repository -> settings -> hooks -> WebHook URLs**

Setting up the AMI
--
**Minimum requirements**
- Web server
- PHP 5.3.3+ compiled with the cURL extension
- cURL 7.16.2+ compiled with OpenSSL and zlib
- git 
- php5-json



**Steps to configure AMI**

2. Change document root ownership to web-server user

 ```shell
 sudo chown -R www-data:www-data /var/www
 ```
3. Login as web-server user

 ```shell
 sudo su www-data
 ```
4. Change directory to web-server home directory

 ```shell
 cd ~
 ```
5. [Generate SSH keys]
6. Create empty repository

 ```shell
 git init
 ```
7. Add a remote origin

 ```shell
 git remote add origin git@github.com:username/repository.git
 ```
8. Download the latest from remote repository without trying to merge or rebase anything

 ```shell
 git fetch --all
 ```
9. Pull

 ```shell
 git pull origin master
 ```
10. Logout of www-data user
11. Configure to run a startup script

 ```shell
 sudo nano /etc/rc.local
 ```
 
 Add
 ```shell
 su - www-data -c /etc/init.d/pull > /home/ubuntu/pull.log
 ```
 before
 ```shell
 exit 0
 ```
12. Create the startup script

 ```shell
 sudo nano /etc/init.d/pull
 ```
 
 Add the following lines
 
 ```shell
 #!/bin/sh
 php /var/www/aws_autoscaling_code_deployer/index.php update
 ```
 
 Change file permission to 777

 ```shell
 sudo chmod +x /etc/init.d/pull
 ```

Contributing
=====================

1. Fork it!
2. Create your feature branch: ```git checkout -b my-new-feature```
3. Commit your changes: ```git commit -m 'Add some feature'```
4. Push to the branch: ```git push origin my-new-feature```
5. Submit a pull request

Get in touch
=======
Joy Prakash Sharma

droidlabour@gmail.com

[Generate SSH keys]:https://help.github.com/articles/generating-ssh-keys
[Github webhook URL]:https://help.github.com/articles/post-receive-hooks
