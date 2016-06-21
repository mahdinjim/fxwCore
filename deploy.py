#this script is used for deploying fxw client application to server
#use this file only in your local computer to deploy changes
#configure deploy.json to be able to deploy your solution
import json
from pprint import pprint
import sys
import pxssh
import getpass


#check the count of argument user must specify if it's production or staging or pre-production
if len(sys.argv)!=2:
	print("Please provide the deployment target in command args example <deploy.py production>")
	exit()
else:
	if sys.argv[1] not in ["production","staging","pre-production"]:
		print("Possible target values are production, staging, pre-production")
		exit();
	else:
		target = sys.argv[1]
print("Deploying to "+target+"...") 

#setp 1: get the config file
try:
	with open('deploy.json') as data_file:    
    		data = json.load(data_file)
except (IOError):
	print("can't find configuration file please create deploy.json file")
	exit()
#first step connect to ssh make sure you have a public/private key configured
try:
	s = pxssh.pxssh()
	if not s.login (data[target]['serverip'],data[target]['ssh_username']):
	    print "SSH session failed on login."
	    exit()
	else:
	    print "SSH session login successful"
	    s.sendline("cd "+data[target]['project_folder'])
	    s.prompt()
	    #get the new code from git branch for this version we assume that you are either using ssh key or already stored your creds in server
	    s.sendline("git pull origin "+data[target]['git_branch'])
	    #update composer
	    #s.sendline("composer update --no-dev --optimize-autoloader")
	    #update database
	    s.sendline("php app/console doctrine:schema:update --force")
	    #clear cache
	    s.sendline("php app/console cache:clear --env=prod --no-debug")
	    #give permission
	    s.sendline("chmod -R 777 app/cache")
	    s.sendline("chmod -R 777 app/logs")
	    s.prompt()
	    #production ready
	    print("Build done")
	    s.logout()
except pxssh.ExceptionPxssh, e:
    print "pxssh failed on login."
    print str(e)
    exit()