require 'rubygems'
require 'rake'
require 'yaml'

require 'rake/testtask'

puts "\e[1;33;40mThese tests perform modifications to account settings and delete profiles associated with the account -
DO NOT RUN THEM ON A PRODUCTION ACCOUNT!\e[0m"
print "Do you want to continue (y/n) ?"
if ($stdin.gets.chomp! == 'y')
  Rake::TestTask.new(:test) do |test|
    test.libs << '.' << 'test'
    test.pattern = 'test/**/*_test.rb'
    test.verbose = false
  end
else
  exit
end