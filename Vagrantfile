# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu/xenial64"

  config.vm.network "forwarded_port", guest: 8181, host: 8181
  config.vm.network "forwarded_port", guest: 8182, host: 8182
  config.vm.network "forwarded_port", guest: 8183, host: 8183
  config.vm.network "forwarded_port", guest: 8184, host: 8184
  config.vm.network "forwarded_port", guest: 8186, host: 8186

  config.vm.provider "virtualbox" do |vb|
    vb.name = "open-loyalty"
    vb.cpus = 2
    vb.customize ["modifyvm", :id, "--cpuexecutioncap", "100"]

    vb.memory = 3096

    # enable APIC (multicore)
    vb.customize ["modifyvm", :id, "--ioapic", "on"]

    # network adapters performance
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
  end

  config.vm.provision :docker
  config.vm.provision "shell", path: "bootstrap.sh"

end
