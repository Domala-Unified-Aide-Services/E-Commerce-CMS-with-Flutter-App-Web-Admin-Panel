import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_service.dart';
import '../constants.dart';

class AuthProvider with ChangeNotifier {
  bool _isLoggedIn = false;
  String? _token;
  int? _userId;
  String? _email;
  String? _username;

  bool get isLoggedIn => _isLoggedIn;
  String? get token => _token;
  int? get userId => _userId;
  String? get email => _email;
  String? get username => _username;

  // --------------------------------------------------------
  // LOAD USER SESSION (CALLED ON APP START)
  // --------------------------------------------------------
  Future<void> loadUser() async {
    final sp = await SharedPreferences.getInstance();

    _token = sp.getString(PREF_AUTH_TOKEN);             // FIXED: using correct key
    _userId = sp.getInt(PREF_USER_ID);
    _email = sp.getString(PREF_USER_EMAIL);
    _username = sp.getString(PREF_USER_NAME);

    _isLoggedIn = (_token != null && _token!.isNotEmpty);

    notifyListeners();
  }

  // --------------------------------------------------------
  // LOGIN
  // --------------------------------------------------------
  Future<Map<String, dynamic>> login(String email, String password) async {
    final resp = await ApiService().login(email, password);

    if (resp.containsKey('token')) {
      final sp = await SharedPreferences.getInstance();

      // Save token + user
      _token = resp['token'];
      await sp.setString(PREF_AUTH_TOKEN, _token!);

      final user = resp['user'];
      if (user != null) {
        _userId = int.tryParse(user['id'].toString());
        _username = user['username']?.toString();
        _email = user['email']?.toString();

        await sp.setInt(PREF_USER_ID, _userId ?? 0);
        if (_username != null) await sp.setString(PREF_USER_NAME, _username!);
        if (_email != null) await sp.setString(PREF_USER_EMAIL, _email!);
      }

      _isLoggedIn = true;
      notifyListeners();
    }

    return resp;
  }

  // --------------------------------------------------------
  // REGISTER (OPTIONAL)
  // --------------------------------------------------------
  Future<Map<String, dynamic>> register(
      String username, String email, String password) async {
    return await ApiService().register(username, email, password);
  }

  // --------------------------------------------------------
  // LOGOUT
  // --------------------------------------------------------
  Future<void> logout() async {
    final sp = await SharedPreferences.getInstance();

    await sp.remove(PREF_AUTH_TOKEN);         // FIXED: correct token key
    await sp.remove(PREF_USER_ID);
    await sp.remove(PREF_USER_EMAIL);
    await sp.remove(PREF_USER_NAME);

    _token = null;
    _userId = null;
    _email = null;
    _username = null;
    _isLoggedIn = false;

    notifyListeners();
  }
}
