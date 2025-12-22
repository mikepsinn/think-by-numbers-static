module.exports = {
  permalink: function(data) {
    // Remove 'content/' from the beginning of the file path
    return data.page.filePathStem.replace(/^\/content/, '') + '/';
  }
};
