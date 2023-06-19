from flask import Flask, render_template, request

import tensorflow as tf
model = tf.keras.models.load_model('Pneumonia_Detection_model.h5')

import cv2
import numpy as np

app = Flask(__name__)

@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        # Get the uploaded image from the HTML form
        image = request.files['Chest_X-Ray']

        # Read and preprocess the image
        img = cv2.imdecode(np.fromstring(image.read(), np.uint8), cv2.IMREAD_COLOR)
        img = cv2.resize(img, (128, 128))
        img = img.astype(np.float32) / 255.0
        img = np.expand_dims(img, axis=0)

        # Make the prediction
        prediction = model.predict(img)
        pneumonia_probability = prediction[0][0]

        # Render the prediction result in the HTML template
        return render_template('uploads.php', probability=pneumonia_probability)

    # Render the HTML form template
    return render_template('index.php')

if __name__ == '__main__':
    app.run()
