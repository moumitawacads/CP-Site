import SecureLS from 'secure-ls';

class MyUploadAdapter {
  constructor(loader) {
    // The loader is the file upload adapter instance.
    this.loader = loader;
  }

  // Upload the file and return a promise with the image URL.
  async upload() {
    return new Promise((resolve, reject) => {
      this.loader.file.then((file) => {
        const data = new FormData();
        data.append('file', file);
        
        const ls = new SecureLS();
        const token = ls.get('token');

        fetch('/api/whats-new/file-upload', {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body: data,
        })
        .then((response) => response.json())
        .then((data) => {
          if (data && data.file_path) {
            resolve({ default: data.file_path }); // URL returned after successful upload
          } else {
            reject('File upload failed');
          }
        })
        .catch(reject);
      }).catch((error) => {
        reject('Failed to load file');
      });
    });
  }

  // Abort the upload if needed (optional).
  abort() {
    console.log('Upload aborted');
  }
}

// Upload adapter factory
function MyUploadAdapterPlugin(editor) {
  editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
    return new MyUploadAdapter(loader);
  };
}
export default MyUploadAdapterPlugin;