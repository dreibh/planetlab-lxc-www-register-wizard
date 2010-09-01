#
# $Id: PLCWWW.spec 12168 2009-02-22 23:26:24Z thierry $
#
%define url $URL: https://svn.planet-lab.org/svn/www-register-wizard/trunk/www-register-wizard.spec $

%define name www-register-wizard
%define version 4.3
%define taglevel 4

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
Requires: PLCWWW >= 4.3
Requires: PLCAPI >= 4.3

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
* Wed Sep 01 2010 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - www-register-wizard-4.3-4
- can set node as reservable at node-creation time

* Wed Apr 21 2010 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - www-register-wizard-4.3-3
- previous tag had gone bad
- + stephen's tweaks for f12

* Tue Apr 13 2010 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - www-register-wizard-4.3-2
- upgrade to codeigniter 1.7.2 for php-5.3 & f12
- enable rewrite_short_tags to preserve support for <?= ?> tag

* Thu Jul 02 2009 Stephen Soltesz <soltesz@cs.princeton.edu> - www-register-wizard-4.3-1
- better error handling on updating node information.

* Wed Jun 03 2009 Stephen Soltesz <soltesz@cs.princeton.edu> - www-register-wizard-4.3-0
- update version number to match 4.3 for consistency and clarity.

* Wed Jun 03 2009 Stephen Soltesz <soltesz@cs.princeton.edu> - www-register-wizard-4.2-4
- changes to work with 4.3... probably need to update the version information to
- avoid confusion.

* Thu Apr 16 2009 Stephen Soltesz <soltesz@cs.princeton.edu> - www-register-wizard-4.2-3
- bring up to date.

* Mon Mar 16 2009 Stephen Soltesz <soltesz@cs.princeton.edu> - www-register-wizard-4.2-2
- correct typos.
- remove extra print statements.
- remove 'debug' continue buttons.

* Fri Mar 13 2009 Stephen Soltesz <soltesz@cs.princeton.edu> - www-register-wizard-4.2-1
- enforced model check on add-pcu.
- display clearer messages on stage 8, regarding pcu success or failure.

* Fri Feb 27 2009 Stephen Soltesz <soltesz@cs.princeton.edu> -
- Initial creation of spec file.
