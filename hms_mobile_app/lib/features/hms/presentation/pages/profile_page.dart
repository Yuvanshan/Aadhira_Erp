import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:hms_mobile_app/l10n/app_localizations.dart';
import 'package:hms_mobile_app/core/app_config.dart';
import 'package:hms_mobile_app/features/auth_bloc.dart';
import 'package:hms_mobile_app/main.dart';
import '../widgets/shared_widgets.dart';

class ProfilePage extends StatelessWidget {
  final Map<String, dynamic> profile;
  const ProfilePage({Key? key, required this.profile}) : super(key: key);

  void _confirmSignOut(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      backgroundColor: Theme.of(context).colorScheme.surface,
      builder: (context) {
        return Padding(
          padding: const EdgeInsets.all(28.0),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text(
                l10n.signoutConfirmTitle,
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              Text(
                l10n.signoutConfirmMsg,
                style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8)),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.of(context).pop(),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        side: const BorderSide(color: Color(0xFF94A3B8)),
                      ),
                      child: Text(l10n.cancel, style: const TextStyle(color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        Navigator.of(context).pop();
                        context.read<AuthBloc>().add(AuthLogoutRequested());
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.redAccent,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        elevation: 0,
                      ),
                      child: Text(l10n.signout, style: const TextStyle(fontWeight: FontWeight.bold)),
                    ),
                  ),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    
    final String firstName = profile['first_name'] ?? "User";
    final String lastName = profile['last_name'] ?? "";
    final String userName = profile['username'] ?? "N/A";
    final String email = profile['email'] ?? "N/A";
    final String businessId = (profile['business_id'] ?? "N/A").toString();
    final String initialStr = firstName.isNotEmpty ? firstName[0].toUpperCase() : "U";

    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // 👤 USER AVATAR & HEADER CARD
          GlassCard(
            child: Column(
              children: [
                CircleAvatar(
                  backgroundColor: const Color(0xFF6366F1),
                  radius: 36,
                  child: Text(
                    initialStr,
                    style: const TextStyle(fontSize: 28, fontWeight: FontWeight.w900, color: Colors.white),
                  ),
                ),
                const SizedBox(height: 16),
                Text(
                  "$firstName $lastName",
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 4),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.green.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    l10n.active,
                    style: const TextStyle(color: Colors.green, fontSize: 11, fontWeight: FontWeight.bold),
                  ),
                )
              ],
            ),
          ),

          // 📄 ACCOUNT INFORMATION CARD
          GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildInfoRow(l10n.username, userName),
                const Divider(color: Colors.white12, height: 24),
                _buildInfoRow(l10n.email, email),
                const Divider(color: Colors.white12, height: 24),
                _buildInfoRow(l10n.businessId, businessId),
                const Divider(color: Colors.white12, height: 24),
                _buildInfoRow(l10n.serverUrl, AppConfig.serverUrl),
              ],
            ),
          ),

          // ⚙️ THEME & LANGUAGE CONTROLS CARD
          GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Theme Mode Toggler
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Icon(isDark ? Icons.dark_mode : Icons.light_mode, color: const Color(0xFF6366F1)),
                        const SizedBox(width: 12),
                        Text(l10n.theme, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                      ],
                    ),
                    Switch(
                      value: isDark,
                      activeColor: const Color(0xFF6366F1),
                      onChanged: (val) {
                        MahdevHMSApp.themeModeNotifier.value = val ? ThemeMode.dark : ThemeMode.light;
                      },
                    )
                  ],
                ),
                const Divider(color: Colors.white12, height: 24),
                
                // Language Selection Buttons
                Row(
                  children: [
                    const Icon(Icons.translate, color: Color(0xFF6366F1)),
                    const SizedBox(width: 12),
                    Text(l10n.language, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildLangButton(context, "English", const Locale('en')),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: _buildLangButton(context, "தமிழ்", const Locale('ta')),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: _buildLangButton(context, "සිංහල", const Locale('si')),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // 🚪 SIGN OUT ACTION BUTTON
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
            child: ElevatedButton.icon(
              onPressed: () => _confirmSignOut(context),
              icon: const Icon(Icons.logout),
              label: Text(l10n.signout, style: const TextStyle(fontWeight: FontWeight.bold)),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.redAccent.withOpacity(0.12),
                foregroundColor: Colors.redAccent,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                side: BorderSide(color: Colors.redAccent.withOpacity(0.2)),
                elevation: 0,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String title, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(title, style: const TextStyle(fontSize: 13, color: Color(0xFF94A3B8), fontWeight: FontWeight.w500)),
        Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
      ],
    );
  }

  Widget _buildLangButton(BuildContext context, String name, Locale locale) {
    final activeLocale = MahdevHMSApp.localeNotifier.value;
    final isSelected = activeLocale.languageCode == locale.languageCode;
    return ElevatedButton(
      onPressed: () {
        MahdevHMSApp.localeNotifier.value = locale;
      },
      style: ElevatedButton.styleFrom(
        backgroundColor: isSelected ? const Color(0xFF6366F1) : Theme.of(context).colorScheme.surface,
        foregroundColor: isSelected ? Colors.white : const Color(0xFF94A3B8),
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
          side: BorderSide(
            color: isSelected ? const Color(0xFF6366F1) : Colors.white.withOpacity(0.04),
          ),
        ),
      ),
      child: Text(name, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
    );
  }
}
