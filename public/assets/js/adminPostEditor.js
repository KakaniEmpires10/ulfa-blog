document.addEventListener('DOMContentLoaded', () => {
  const config = window.APP?.blogPostEditor;

  if (!config || typeof CKEDITOR === 'undefined') {
    return;
  }

  const editorElement = document.querySelector(config.selector || '[data-ckeditor="blog-post"]');

  if (!editorElement) {
    return;
  }

  const {
    AutoImage,
    BlockQuote,
    Bold,
    ClassicEditor,
    Essentials,
    FindAndReplace,
    Heading,
    Highlight,
    HorizontalLine,
    Image,
    ImageCaption,
    ImageInsert,
    ImageResize,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    Italic,
    Link,
    LinkImage,
    List,
    Paragraph,
    Strikethrough,
    Underline,
  } = CKEDITOR;

  const uploadHeaders = {
    'X-Requested-With': 'XMLHttpRequest',
    [config.csrfHeader]: config.csrfToken,
  };

  const syncCsrfToken = (token) => {
    if (!token) {
      return;
    }

    config.csrfToken = token;
    uploadHeaders[config.csrfHeader] = token;

    document
      .querySelectorAll(`input[name="${config.csrfTokenName}"]`)
      .forEach((input) => {
        input.value = token;
      });

    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');

    if (csrfTokenMeta) {
      csrfTokenMeta.setAttribute('content', token);
    }

    const csrfHeaderMeta = document.querySelector(`meta[name="${config.csrfHeader}"]`);

    if (csrfHeaderMeta) {
      csrfHeaderMeta.setAttribute('content', token);
    }
  };

  class BlogImageUploadAdapter {
    constructor(loader) {
      this.loader = loader;
      this.xhr = null;
    }

    upload() {
      return this.loader.file.then((file) => new Promise((resolve, reject) => {
        this.xhr = new XMLHttpRequest();
        this.xhr.open('POST', config.uploadUrl, true);
        this.xhr.responseType = 'json';

        Object.entries(uploadHeaders).forEach(([header, value]) => {
          this.xhr.setRequestHeader(header, value);
        });

        this.xhr.addEventListener('error', () => {
          reject('Gagal mengupload gambar. Silakan coba lagi.');
        });

        this.xhr.addEventListener('abort', () => {
          reject('Upload gambar dibatalkan.');
        });

        this.xhr.addEventListener('load', () => {
          const response = this.xhr.response || {};

          syncCsrfToken(response.csrfHash || null);

          if (this.xhr.status < 200 || this.xhr.status >= 300 || response.error) {
            reject(response.error?.message || 'Gagal mengupload gambar. Silakan coba lagi.');
            return;
          }

          resolve({
            default: response.url,
          });
        });

        if (this.xhr.upload) {
          this.xhr.upload.addEventListener('progress', (event) => {
            if (!event.lengthComputable) {
              return;
            }

            this.loader.uploadTotal = event.total;
            this.loader.uploaded = event.loaded;
          });
        }

        const formData = new FormData();
        formData.append('upload', file);

        this.xhr.send(formData);
      }));
    }

    abort() {
      if (this.xhr) {
        this.xhr.abort();
      }
    }
  }

  ClassicEditor.create(editorElement, {
    licenseKey: 'GPL',
    plugins: [
      AutoImage,
      BlockQuote,
      Bold,
      Essentials,
      FindAndReplace,
      Heading,
      Highlight,
      HorizontalLine,
      Image,
      ImageCaption,
      ImageInsert,
      ImageResize,
      ImageStyle,
      ImageToolbar,
      ImageUpload,
      Italic,
      Link,
      LinkImage,
      List,
      Paragraph,
      Strikethrough,
      Underline,
    ],
    toolbar: {
      items: [
        'heading',
        '|',
        'bold',
        'italic',
        'underline',
        'strikethrough',
        'highlight',
        '|',
        'link',
        'bulletedList',
        'numberedList',
        'blockQuote',
        'horizontalLine',
        '|',
        'insertImage',
        'findAndReplace',
        '|',
        'undo',
        'redo',
      ],
    },
    heading: {
      options: [
        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
      ],
    },
    highlight: {
      options: [
        { model: 'markerYellow', class: 'marker-yellow', title: 'Soft yellow', color: 'var(--ck-content-highlight-marker-yellow)', type: 'marker' },
        { model: 'markerGreen', class: 'marker-green', title: 'Soft green', color: 'var(--ck-content-highlight-marker-green)', type: 'marker' },
        { model: 'markerPink', class: 'marker-pink', title: 'Soft pink', color: 'var(--ck-content-highlight-marker-pink)', type: 'marker' },
        { model: 'markerBlue', class: 'marker-blue', title: 'Soft blue', color: 'var(--ck-content-highlight-marker-blue)', type: 'marker' },
        { model: 'penRed', class: 'pen-red', title: 'Accent red', color: 'var(--ck-content-highlight-pen-red)', type: 'pen' },
        { model: 'penGreen', class: 'pen-green', title: 'Accent green', color: 'var(--ck-content-highlight-pen-green)', type: 'pen' },
      ],
    },
    image: {
      insert: {
        type: 'block',
      },
      resizeOptions: [
        {
          name: 'resizeImage:original',
          value: null,
          label: 'Original',
        },
        {
          name: 'resizeImage:50',
          value: '50',
          label: '50%',
        },
        {
          name: 'resizeImage:75',
          value: '75',
          label: '75%',
        },
      ],
      styles: ['inline', 'wrapText', 'breakText'],
      toolbar: [
        'imageStyle:inline',
        'imageStyle:wrapText',
        'imageStyle:breakText',
        '|',
        'resizeImage',
        '|',
        'toggleImageCaption',
        'imageTextAlternative',
        'linkImage',
      ],
    },
    link: {
      addTargetToExternalLinks: true,
      defaultProtocol: 'https://',
    },
    placeholder: editorElement.dataset.placeholder || 'Tulis konten blog Anda...',
  })
    .then((editor) => {
      editor.plugins.get('FileRepository').createUploadAdapter = (loader) => new BlogImageUploadAdapter(loader);
      window.APP.blogPostEditorInstance = editor;
    })
    .catch((error) => {
      console.error('CKEditor failed to initialize.', error);
    });
});
