from distutils.core import setup

setup(
    name='KalturaClient',
    version='1.0.0',
    url='http://www.kaltura.com/api_v3/testme/client-libs.php',
    packages=['KalturaClient', 'KalturaClient.Plugins'],
    license='AGPL',
    description='A Python module for accessing the Kaltura API.',
    long_description=open('README.txt').read(),
)
