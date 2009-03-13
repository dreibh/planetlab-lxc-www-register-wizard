#
# $Id: PLCWWW.spec 12168 2009-02-22 23:26:24Z thierry $
#
%define url $URL: https://svn.planet-lab.org/svn/www-register-wizard/trunk/www-register-wizard.spec $

%define name www-register-wizard
%define version 4.2
%define taglevel 1

%define release %{taglevel}%{?pldistro:.%{pldistro}}%{?date:.%{date}}

Summary: Registration Wizard for Nodes and PCUs
Name: %{name}
Version: %{version}
Release: %{release}
License: PlanetLab
Group: System Environment/Daemons
Source0: %{name}-%{version}.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root
BuildArch: noarch

Vendor: PlanetLab
Packager: PlanetLab Central <support@planet-lab.org>
Distribution: PlanetLab %{plrelease}
URL: %(echo %{url} | cut -d ' ' -f 2)

# We use set everywhere
#Requires: httpd >= 2.0
Requires: PLCWWW >= 4.2
Requires: PLCAPI >= 4.2

%description

The www-register-wizard provides a web interface for MyPLC that integrates and
serializes the steps required to register, configure and verify that a node is
running correctly.  This is an improvement upon and replacement for the existing 'Add Node' and
'Add PCU' forms.

%prep
%setup -q

%build
echo "There is no build stage for this component."
echo "All files just need to be installed as is from the codebase."

%install
rm -rf $RPM_BUILD_ROOT

echo "* www-register-wizard: Installing www-register-wizard pages"

mkdir -p $RPM_BUILD_ROOT/var/www/html
# let's be conservative and exclude codebase files, though there should not be any
rsync -a --exclude \*.spec --exclude .svn ./ $RPM_BUILD_ROOT/var/www/html/registerwizard

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root,-)
/var/www/html/registerwizard

%changelog
* Fri Mar 13 2009 Stephen Soltesz <soltesz@cs.princeton.edu> - www-register-wizard-4.2-1
- enforced model check on add-pcu.
- display clearer messages on stage 8, regarding pcu success or failure.

* Fri Feb 27 2009 Stephen Soltesz <soltesz@cs.princeton.edu> -
- Initial creation of spec file.
