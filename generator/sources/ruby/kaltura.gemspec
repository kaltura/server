require 'rake'

spec = Gem::Specification.new do |s| 
  s.name = "kaltura-ruby-client"
  s.version = "1.0"
  s.date = '2014-01-16'
  s.licenses = ['AGPL-3.0']
  s.author = "Kaltura Inc."
  s.email = "community@kaltura.com"
  s.homepage = "http://www.kaltura.com/"
  s.summary = "A gem implementation of Kaltura's Ruby Client"
  s.description = "A gem implementation of Kaltura's Ruby Client - https://github.com/kaltura/server/tree/master/generator/sources/ruby"
  s.files = FileList["lib/**/*.rb","Rakefile","README", "agpl.txt", "kaltura.yml"].to_a
  s.test_files = FileList["{test}/test_helper.rb", "{test}/**/*test.rb", "{test}/media/*"].to_a
  s.add_dependency('rest-client')
end
