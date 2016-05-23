# Getting A Local Environment Setup

## Requirements

This project uses Vagrant to qickly setup a VM running XIVSync that contains everything it needs (NodeJS and MySQL). [You can download Vagrant here](https://www.vagrantup.com/), once you have it downloaded, clone this repository and navigate to the folder in Windows Explorer.

## Getting started

You should see a file called `start_vm.bat`, double click it to start. This script contains 2 commands, the first command installs a plugin called [Vagrant Hostmanager](https://github.com/devopsgroup-io/vagrant-hostmanager), this means you do not need to modify your host file to access the VM domain. The second command is `vagrant up` which boots the VM.

Once it has finished, you should be able to navigate to:

- `http://xivsync.dev/`
