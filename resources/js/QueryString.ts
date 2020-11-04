interface Result {
  [key: string]: string,
};

export default (function () {
  var results: Result = {};
  var hash: string[];

  if (window.location.href.indexOf('?') !== -1) {
    var querystring = window.location.href.slice(window.location.href.indexOf('?') + 1);
    var hashes = querystring.split('&');

    for (var i = 0; i < hashes.length; i++) {
      hash = hashes[i].split('=');
      results[hash[0]] = hash[1];
    }
  }

  return results;
})();
