import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:hms_mobile_app/l10n/app_localizations.dart';
import 'package:hms_mobile_app/core/app_config.dart';
import 'package:hms_mobile_app/core/discovery_service.dart';
import 'package:hms_mobile_app/features/auth_bloc.dart';
import 'package:hms_mobile_app/core/l10n_extension.dart';
import '../../../hms/presentation/widgets/shared_widgets.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({Key? key}) : super(key: key);

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  final _usernameController = TextEditingController(text: "admin");
  final _passwordController = TextEditingController();
  bool _isAutoDiscovering = false;

  void _submit() {
    if (!_formKey.currentState!.validate()) return;

    context.read<AuthBloc>().add(
      AuthLoginRequested(
        url: AppConfig.serverUrl,
        clientId: AppConfig.clientId,
        clientSecret: AppConfig.clientSecret,
        username: _usernameController.text.trim(),
        password: _passwordController.text,
      ),
    );
  }

  void _autoDiscover() async {
    setState(() {
      _isAutoDiscovering = true;
    });

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text("Searching for local ERP server on your Wi-Fi..."),
        duration: Duration(seconds: 4),
      ),
    );

    final result = await DiscoveryService.discoverLocalServer();

    setState(() {
      _isAutoDiscovering = false;
    });

    if (result != null) {
      await AppConfig.save(
        url: result['url'],
        id: result['client_id'],
        secret: result['client_secret'],
      );

      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: const Text("Server Discovered!", style: TextStyle(fontWeight: FontWeight.bold)),
          content: Text(
            "Found business: ${result['business_name']}\n"
            "URL: ${result['url']}\n\n"
            "Connection has been automatically configured!",
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text("OK", style: TextStyle(color: Color(0xFF6366F1))),
            )
          ],
        ),
      );
    } else {
      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: const Text("Server Not Found", style: TextStyle(fontWeight: FontWeight.bold)),
          content: const Text(
            "Could not discover local server automatically.\n\n"
            "Please check:\n"
            "1. Your phone and PC are on the SAME Wi-Fi network.\n"
            "2. The server application is running on your PC.\n"
            "3. Or click the gear icon to configure manually.",
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text("Close", style: TextStyle(color: Color(0xFF6366F1))),
            )
          ],
        ),
      );
    }
  }

  void _openManualConfig() {
    final nameController = TextEditingController();
    final urlController = TextEditingController(text: "http://");
    final idController = TextEditingController();
    final secretController = TextEditingController();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      backgroundColor: Theme.of(context).colorScheme.surface,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Padding(
              padding: EdgeInsets.only(
                left: 28.0,
                right: 28.0,
                top: 28.0,
                bottom: MediaQuery.of(context).viewInsets.bottom + 28.0,
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Text(
                    "Manage Business Profiles",
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
                  ),
                  const SizedBox(height: 20),
                  
                  if (AppConfig.savedBusinesses.isNotEmpty) ...[
                    const Text("Saved Profiles", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF94A3B8))),
                    const SizedBox(height: 8),
                    Container(
                      constraints: const BoxConstraints(maxHeight: 150),
                      child: ListView.builder(
                        shrinkWrap: true,
                        itemCount: AppConfig.savedBusinesses.length,
                        itemBuilder: (context, idx) {
                          final profile = AppConfig.savedBusinesses[idx];
                          final isCurrent = profile['name'] == AppConfig.activeBusinessName;
                          return ListTile(
                            contentPadding: EdgeInsets.zero,
                            title: Text(
                              profile['name'] ?? '',
                              style: TextStyle(fontWeight: isCurrent ? FontWeight.bold : FontWeight.normal),
                            ),
                            subtitle: Text(profile['url'] ?? '', style: const TextStyle(fontSize: 11)),
                            trailing: isCurrent
                                ? const Icon(Icons.check_circle, color: Colors.green, size: 20)
                                : IconButton(
                                    icon: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 20),
                                    onPressed: () async {
                                      await AppConfig.deleteProfile(profile['name'] ?? '');
                                      setModalState(() {});
                                      setState(() {});
                                    },
                                  ),
                          );
                        },
                      ),
                    ),
                    const Divider(height: 24, color: Colors.white12),
                  ],

                  const Text("Add New Profile", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF94A3B8))),
                  const SizedBox(height: 12),
                  TextField(
                    controller: nameController,
                    decoration: const InputDecoration(labelText: "Profile Name (e.g. Hotel A)"),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: urlController,
                    decoration: const InputDecoration(labelText: "Server URL"),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: idController,
                    decoration: const InputDecoration(labelText: "OAuth Client ID"),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: secretController,
                    decoration: const InputDecoration(labelText: "OAuth Client Secret"),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: () async {
                      if (nameController.text.trim().isEmpty || urlController.text.trim().isEmpty) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text("Please fill Profile Name and URL")),
                        );
                        return;
                      }
                      await AppConfig.saveProfile(
                        name: nameController.text.trim(),
                        url: urlController.text.trim(),
                        id: idController.text.trim(),
                        secret: secretController.text.trim(),
                      );
                      Navigator.of(context).pop();
                      setState(() {});
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text("Profile added & selected successfully!")),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF6366F1),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                    child: const Text("Add & Activate Profile", style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            );
          }
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      body: Stack(
        children: [
          // Background Gradient decoration
          Positioned.fill(
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: isDark
                      ? [const Color(0xFF020617), const Color(0xFF0F172A)]
                      : [const Color(0xFFF8FAFC), const Color(0xFFE2E8F0)],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
              ),
            ),
          ),
          Positioned(
            top: -100,
            right: -100,
            child: CircleAvatar(
              radius: 200,
              backgroundColor: const Color(0xFF6366F1).withOpacity(0.06),
            ),
          ),

          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                child: Padding(
                  padding: const EdgeInsets.all(24.0),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Config Actions Row
                      Row(
                        mainAxisAlignment: MainAxisAlignment.end,
                        children: [
                          IconButton(
                            icon: const Icon(Icons.settings, color: Color(0xFF6366F1)),
                            onPressed: _openManualConfig,
                          ),
                        ],
                      ),
                      const SizedBox(height: 10),

                      // Logo Header
                      Container(
                        width: 80,
                        height: 80,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: const Color(0xFF6366F1).withOpacity(0.12),
                        ),
                        child: const Icon(
                          Icons.apartment,
                          color: Color(0xFF6366F1),
                          size: 40,
                        ),
                      ),
                      const SizedBox(height: 24),
                      Text(
                        l10n.welcomeBack,
                        style: TextStyle(
                          fontSize: 26,
                          fontWeight: FontWeight.w900,
                          color: isDark ? Colors.white : Colors.black87,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 8),
                      Text(
                        "Manage your Hotel & Bookings from Anywhere",
                        style: TextStyle(
                          fontSize: 13,
                          color: isDark ? Colors.white70 : Colors.black54,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 36),

                      BlocListener<AuthBloc, AuthState>(
                        listener: (context, state) {
                          if (state is AuthFailure) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text(state.errorMessage),
                                backgroundColor: Colors.redAccent,
                              ),
                            );
                          }
                        },
                        child: GlassCard(
                          padding: const EdgeInsets.all(24.0),
                          child: Form(
                            key: _formKey,
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.stretch,
                              children: [
                                if (AppConfig.savedBusinesses.isNotEmpty) ...[
                                  DropdownButtonFormField<Map<String, String>>(
                                    value: AppConfig.savedBusinesses.firstWhere(
                                      (b) => b['name'] == AppConfig.activeBusinessName,
                                      orElse: () => AppConfig.savedBusinesses.first,
                                    ),
                                    decoration: const InputDecoration(
                                      labelText: "Active Business Profile",
                                      prefixIcon: Icon(Icons.business),
                                    ),
                                    items: AppConfig.savedBusinesses.map((business) {
                                      return DropdownMenuItem<Map<String, String>>(
                                        value: business,
                                        child: Text(
                                          business['name'] ?? 'Unknown',
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      );
                                    }).toList(),
                                    onChanged: (profile) async {
                                      if (profile != null) {
                                        await AppConfig.activateProfile(profile);
                                        setState(() {});
                                      }
                                    },
                                  ),
                                  const SizedBox(height: 16),
                                ],
                                TextFormField(
                                  controller: _usernameController,
                                  decoration: InputDecoration(
                                    labelText: l10n.username,
                                    prefixIcon: const Icon(Icons.person_outline),
                                  ),
                                  validator: (val) {
                                    if (val == null || val.trim().isEmpty) {
                                      return 'Please enter username';
                                    }
                                    return null;
                                  },
                                ),
                                const SizedBox(height: 16),
                                TextFormField(
                                  controller: _passwordController,
                                  obscureText: true,
                                  decoration: InputDecoration(
                                    labelText: l10n.password,
                                    prefixIcon: const Icon(Icons.lock_outline),
                                  ),
                                  validator: (val) {
                                    if (val == null || val.isEmpty) {
                                      return 'Please enter password';
                                    }
                                    return null;
                                  },
                                ),
                                const SizedBox(height: 24),
                                BlocBuilder<AuthBloc, AuthState>(
                                  builder: (context, state) {
                                    final isLoading = state is AuthLoading;
                                    return ElevatedButton(
                                      onPressed: isLoading ? null : _submit,
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: const Color(0xFF6366F1),
                                        foregroundColor: Colors.white,
                                        padding: const EdgeInsets.symmetric(vertical: 16),
                                        shape: RoundedRectangleBorder(
                                          borderRadius: BorderRadius.circular(16),
                                        ),
                                        elevation: 0,
                                      ),
                                      child: isLoading
                                          ? const SizedBox(
                                              width: 20,
                                              height: 20,
                                              child: CircularProgressIndicator(
                                                color: Colors.white,
                                                strokeWidth: 2,
                                              ),
                                            )
                                          : Text(
                                              l10n.login,
                                              style: const TextStyle(
                                                fontWeight: FontWeight.bold,
                                                fontSize: 16,
                                              ),
                                            ),
                                    );
                                  },
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Auto-Discovery Callout Button
                      OutlinedButton.icon(
                        onPressed: _isAutoDiscovering ? null : _autoDiscover,
                        icon: _isAutoDiscovering
                            ? const SizedBox(
                                width: 16,
                                height: 16,
                                child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF6366F1)),
                              )
                            : const Icon(Icons.wifi_find, size: 18),
                        label: Text(
                          _isAutoDiscovering ? "Searching Subnet..." : "Auto-Discover Server URL",
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: const Color(0xFF6366F1),
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          side: const BorderSide(color: Color(0xFF6366F1), width: 1.5),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
