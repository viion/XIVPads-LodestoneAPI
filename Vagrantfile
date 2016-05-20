# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
    config.vm.box = "ubuntu/trusty64"
    config.vm.provision :shell, path: "vm/provision.sh"
    config.vm.network "forwarded_port", guest: 80, host: 3000

    config.vm.provider :virtualbox do |vb|
        vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        vb.memory = 1024
        vb.cpus = 2
    end

    config.vm.network "private_network", ip: "14.14.14.14"
    config.vm.hostname = "xivsync.dev"
    config.vm.synced_folder ".", "/home/vagrant/", type: "nfs"

    config.hostmanager.enabled = true
    config.hostmanager.manage_host = true
end
