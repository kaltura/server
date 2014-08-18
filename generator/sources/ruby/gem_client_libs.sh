#!/bin/bash -e 
#===============================================================================
#          FILE: gem_client_libs.sh
#         USAGE: ./gem_client_libs.sh 
#   DESCRIPTION: builds the Ruby clientlibs Gem and pushes it to rubygems.com
#		 requires the API key under ~/.gem/credentials 
#       OPTIONS: ---
#  REQUIREMENTS: ---
#          BUGS: ---
#         NOTES: ---
#        AUTHOR: Jess Portnoy (), <jess.portnoy@kaltura.com>
#  ORGANIZATION: Kaltura, inc.
#       CREATED: 08/18/2014 05:06:52 PM IDT
#      REVISION:  ---
#===============================================================================

#set -o nounset                              # Treat unset variables as an error
API_VERSION=16-01-2014
TMPDIR=/tmp/ruby_$API_VERSION
mkdir -p $TMPDIR
cd $TMPDIR
RELEASE_DATE=`echo $API_VERSION |awk -F '-' '{print $3"-"$2"-"$1}'`
wget http://cdnbakmi.kaltura.com/content/clientlibs/ruby_$API_VERSION.tar.gz -O ruby_$API_VERSION.tar.gz 
tar zxf ruby_$API_VERSION.tar.gz
cd ruby
sed -i "s@^\s*s.date\s*=\s*.*@s.date = '$RELEASE_DATE'@" kaltura.gemspec
GEM_VER=`grep s.version kaltura.gemspec |awk -F '"' '{print $2}'`
gem build kaltura.gemspec
gem push kaltura-ruby-client-${GEM_VER}.gem
