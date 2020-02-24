--TEST--
crc32c() basic tests
--SKIPIF--
<?php
if (!extension_loaded('crc32c')) {
    echo 'skip';
}
?>
--FILE--
<?php
function h($str)
{
    return bin2hex(crc32c($str));
}

echo h(''), "\n";
echo h('a'), "\n";
echo h('ab'), "\n";
echo h('abc'), "\n";
echo h('abcd'), "\n";
echo h('abcde'), "\n";
echo h('abcdef'), "\n";
echo h('abcdefg'), "\n";
echo h('abcdefgh'), "\n";
echo h('abcdefghi'), "\n";
echo h('abcdefghij'), "\n";
echo h('abcdefghijklmnopqrstuvwxyz'), "\n";
echo h('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), "\n";
echo h('12345678901234567890123456789012345678901234567890123456789012345678901234567890'), "\n";
echo h('message digest'), "\n";
echo h("I can't remember anything"), "\n";
echo h("I can't remember anything" . "Can’t tell if this is true or dream"), "\n";
echo h('Discard medicine more than two years old.'), "\n";
echo h('He who has a shady past knows that nice guys finish last.'), "\n";
echo h("I wouldn't marry him with a ten foot pole."), "\n";
echo h("Free! Free!/A trip/to Mars/for 900/empty jars/Burma Shave"), "\n";
echo h("The days of the digital watch are numbered.  -Tom Stoppard"), "\n";
echo h("Nepal premier won't resign."), "\n";
echo h("For every action there is an equal and opposite government program."), "\n";
echo h("His money is twice tainted: 'taint yours and 'taint mine."), "\n";
echo h("There is no reason for any individual to have a computer in their home. -Ken Olsen, 1977"), "\n";
echo h("It's a tiny change to the code and not completely disgusting. - Bob Manchek"), "\n";
echo h("size:  a.out:  bad magic"), "\n";
echo h("The major problem is with sendmail.  -Mark Horton"), "\n";
echo h("Give me a rock, paper and scissors and I will move the world.  CCFestoon"), "\n";
echo h("If the enemy is within range, then so are you."), "\n";
echo h("It's well we cannot hear the screams/That we create in others' dreams."), "\n";
echo h("You remind me of a TV show, but that's all right: I watch it anyway."), "\n";
echo h("C is as portable as Stonehedge!!"), "\n";
echo h("Even if I could be Shakespeare, I think I should still choose to be Faraday. - A. Huxley"), "\n";
echo h("The fugacity of a constituent in a mixture of gases at a given temperature is proportional to its mole fraction.  Lewis-Randall Rule"), "\n";
echo h("How can you write a big system without C++?  -Paul Glick"), "\n";
echo h("\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\v\f\r\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f !\"#\$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff"), "\n";
?>
--EXPECT--
00000000
c1d04330
e2a22936
364b3fb7
92c80a31
c450d697
53bceff1
e627f441
0a9421b7
2ddc99fc
e6599437
9ee6ef25
a245d57d
477a6781
02bd79d0
5e405e93
516ad412
b2cc01fe
0e28207f
be93f964
9e3be0c3
f505ef04
85d3dc82
c5142380
75eb77dd
91ebe9f7
f0b1168e
572b74e2
8a58a6d5
9c426c50
735400a4
bec49c95
a95a2079
de2e65c5
297a88ed
66ed1d8b
dcded527
9c44184b
