#!/usr/bin/env python
# coding: utf-8

# In[1]:


import tensorflow
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Conv2D
from tensorflow.keras.layers import MaxPooling2D
from tensorflow.keras.layers import Flatten
from tensorflow.keras.layers import Dense
from tensorflow.keras.preprocessing.image import ImageDataGenerator
import numpy as np
from tensorflow.keras.preprocessing import image
import os
import matplotlib.pyplot as plt
import cv2


# In[14]:


image_dims = 128
batch_size = 64


# In[15]:


model = Sequential()

model.add(Conv2D(64 , (3,3) , activation = 'relu' , input_shape= (image_dims, image_dims, 3)))
model.add(MaxPooling2D((2,2)))

model.add(Conv2D(64 , (3,3) , activation = 'relu'))
model.add(MaxPooling2D((2,2)))

model.add(Conv2D(64 , (3,3) , activation = 'relu'))
model.add(MaxPooling2D((2,2)))

model.add(Flatten())

model.add(Dense(128, activation = 'relu'))

model.add(Dense(1, activation = 'sigmoid'))


# In[16]:


model.summary()


# In[17]:


model.compile(optimizer = 'adam', loss = 'binary_crossentropy', metrics = ['accuracy'])


# In[18]:


input_path = 'chest_xray/'

# Generate Training data:
training_data_generator =  ImageDataGenerator(rescale = 1./255,
                                             shear_range = 0.2,
                                             zoom_range = 0.2,
                                             horizontal_flip= True)

training_gen = training_data_generator.flow_from_directory(directory=input_path+'train',
                                                          target_size=(image_dims,image_dims),
                                                          batch_size=batch_size,
                                                          class_mode='binary')


# In[19]:


# Generate test data:

validation_data_generator = ImageDataGenerator(rescale= 1./255)

validation_gen = validation_data_generator.flow_from_directory(directory= input_path+ 'val',
                                                     target_size=(image_dims,image_dims),
                                                     batch_size= batch_size,
                                                     class_mode= 'binary')
                                                     


# In[20]:


# find the model accuracy:
epochs = 10
history = model.fit_generator(training_gen,
                             steps_per_epoch= 10,
                             epochs = epochs,
                             validation_data=validation_gen,
                             validation_steps= validation_gen.samples)


# In[22]:


test_data_generator = ImageDataGenerator(rescale = 1/255)

test_gen = test_data_generator.flow_from_directory(directory = input_path + 'test',
    target_size = (image_dims, image_dims),
    batch_size = 128, 
    class_mode = 'binary'
)

eval_result = model.evaluate_generator(test_gen, 624)
print('loss rate at evaluation data :', eval_result[0])
print('accuracy rate at evaluation data :', eval_result[1])


# In[ ]:




