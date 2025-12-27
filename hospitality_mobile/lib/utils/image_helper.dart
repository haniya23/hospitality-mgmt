import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_cropper/image_cropper.dart';
import 'package:flutter_image_compress/flutter_image_compress.dart';
import 'package:image_picker/image_picker.dart';

class ImageHelper {
  ImageHelper._();

  static final ImageHelper _instance = ImageHelper._();

  static ImageHelper get instance => _instance;

  final ImagePicker _imagePicker = ImagePicker();
  final ImageCropper _imageCropper = ImageCropper();

  Future<XFile?> pickImage({
    ImageSource source = ImageSource.gallery,
    int imageQuality = 85,
  }) async {
    return await _imagePicker.pickImage(
      source: source,
      imageQuality: imageQuality,
    );
  }

  Future<CroppedFile?> crop({
    required XFile file,
    CropStyle cropStyle = CropStyle.rectangle,
  }) async {
    return await _imageCropper.cropImage(
      sourcePath: file.path,
      compressQuality: 85,
      uiSettings: [
        AndroidUiSettings(
          toolbarTitle: 'Crop Image',
          toolbarColor: const Color(0xFF1E293B),
          toolbarWidgetColor: Colors.white,
          initAspectRatio: CropAspectRatioPreset.original,
          lockAspectRatio: false,
        ),
        IOSUiSettings(
          title: 'Crop Image',
        ),
      ],
    );
  }

  Future<XFile?> compress({
    required File file,
  }) async {
      int quality = 85;
      final targetSize = 512 * 1024; // 512KB in bytes

      // Get initial file size
      var bytes = await file.readAsBytes();
      
      // If already small enough, return original
      if (bytes.lengthInBytes <= targetSize) {
        return XFile(file.path);
      }

      final dir = await Directory.systemTemp.createTemp();
      
      XFile? result;
      
      // Iteratively compress
      while (bytes.lengthInBytes > targetSize && quality > 10) {
        final targetPath = '${dir.absolute.path}/${DateTime.now().millisecondsSinceEpoch}_compressed_$quality.jpg';
        
        result = await FlutterImageCompress.compressAndGetFile(
          file.absolute.path,
          targetPath,
          quality: quality,
          minWidth: 1920,
          minHeight: 1080,
        );

        if (result != null) {
           bytes = await result.readAsBytes();
        }
        
        quality -= 15; // Reduce quality aggressively if needed
      }

      return result ?? XFile(file.path);
  }
}
