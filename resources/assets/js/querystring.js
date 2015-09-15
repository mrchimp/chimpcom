// Get QueryString
var QueryString = [], hash;
var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
for(var i = 0; i < hashes.length; i++)
{
    hash = hashes[i].split('=');
    QueryString.push(hash[0]);
    QueryString[hash[0]] = hash[1];
}