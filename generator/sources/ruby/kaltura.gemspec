require 'rake'

spec = Gem::Specification.new do |s| 
  s.name = "kaltura-ruby-client"
  s.version = "1.0"
  s.date = '2012-04-16'
  s.author = "Kaltura Inc."
  s.email = "info@kaltura.com"
  s.homepage = "http://www.kaltura.com/"
  s.summary = "A gem implementation of Kaltura's Ruby Client"
  s.description = "A gem implementation of Kaltura's Ruby Client."
  s.files = FileList["**/*.rb", "license.txt","Rakefile","README", "agpl.txt", "kaltura.yml"].to_a
  s.test_files = FileList["{test}/**/*test.rb"].to_a
  s.add_dependency('rest-client')
end
