SVMM - Simple Virtual Machine Manager
----
A simple VM manager with bash backend that runs on flatfile databases. It requires 
no setup (except setting properties in the config.php script). SVMM is released 
under the CC-BY-NC-SA v3.0 unported.

SVMM was written by Chris Dorman, 2020 <https://cddo.cf>

Requirements
----
* PHP5+
* Qemu-KVM installed and configured for KVM
* Super user privleges

Setup
----
* Modify config.php to your needs
* Visit page to generate svmm_db for flat file user database
* Execute SVMM backend as SU: sudo ./start-svmm start

ChangeLog
----
9/24/2020 - v1.0
* Working PHP front end with user registeration and login
* Working bash back end for server management
* Allows each user to create 1 VM
* Allows user ability to start / stop VM
* PHP email validation via filter_var
