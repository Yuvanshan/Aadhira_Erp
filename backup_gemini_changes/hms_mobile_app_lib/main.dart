import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:hms_mobile_app/core/api_service.dart';
import 'package:hms_mobile_app/core/app_config.dart';
import 'package:hms_mobile_app/features/auth_bloc.dart';
import 'package:hms_mobile_app/features/hms_bloc.dart';
import 'package:hms_mobile_app/l10n/app_localizations.dart';

import 'features/auth/presentation/pages/login_page.dart';
import 'features/hms/presentation/pages/dashboard_page.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppConfig.load();
  
  final apiService = ApiService();
  runApp(
    MultiBlocProvider(
      providers: [
        BlocProvider<AuthBloc>(create: (context) => AuthBloc(apiService)),
        BlocProvider<HmsBloc>(create: (context) => HmsBloc(apiService)),
      ],
      child: const MahdevHMSApp(),
    ),
  );
}

class MahdevHMSApp extends StatelessWidget {
  const MahdevHMSApp({Key? key}) : super(key: key);

  // ValueNotifier selectors for runtime Locale & Theme configuration
  static final ValueNotifier<Locale> localeNotifier = ValueNotifier(const Locale('en'));
  static final ValueNotifier<ThemeMode> themeModeNotifier = ValueNotifier(ThemeMode.dark);

  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder<Locale>(
      valueListenable: localeNotifier,
      builder: (context, currentLocale, _) {
        return ValueListenableBuilder<ThemeMode>(
          valueListenable: themeModeNotifier,
          builder: (context, currentThemeMode, _) {
            return MaterialApp(
              title: 'Mahdev ERP',
              debugShowCheckedModeBanner: false,
              locale: currentLocale,
              localizationsDelegates: const [
                AppLocalizations.delegate,
                GlobalMaterialLocalizations.delegate,
                GlobalWidgetsLocalizations.delegate,
                GlobalCupertinoLocalizations.delegate,
              ],
              supportedLocales: const [
                Locale('en'), // English
                Locale('ta'), // Tamil
                Locale('si'), // Sinhala
              ],
              themeMode: currentThemeMode,
              // Modern Light Theme
              theme: ThemeData(
                useMaterial3: true,
                brightness: Brightness.light,
                scaffoldBackgroundColor: const Color(0xFFF3F4F6),
                colorScheme: const ColorScheme.light(
                  primary: Color(0xFF6366F1),
                  secondary: Color(0xFF8B5CF6),
                  surface: Colors.white,
                  background: Color(0xFFF3F4F6),
                  onPrimary: Colors.white,
                ),
                cardTheme: const CardThemeData(
                  color: Colors.white,
                  elevation: 2,
                  margin: EdgeInsets.zero,
                ),
                inputDecorationTheme: InputDecorationTheme(
                  filled: true,
                  fillColor: Colors.white,
                  labelStyle: const TextStyle(color: Color(0xFF4B5563), fontSize: 13, fontWeight: FontWeight.w600),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: const BorderSide(color: Color(0xFF6366F1), width: 2),
                  ),
                ),
              ),
              // Modern Dark Theme
              darkTheme: ThemeData(
                useMaterial3: true,
                brightness: Brightness.dark,
                scaffoldBackgroundColor: const Color(0xFF070B13),
                colorScheme: const ColorScheme.dark(
                  primary: Color(0xFF6366F1),
                  secondary: Color(0xFF8B5CF6),
                  surface: Color(0xFF131A26),
                  background: Color(0xFF070B13),
                  onPrimary: Colors.white,
                ),
                cardTheme: const CardThemeData(
                  color: Color(0xFF131A26),
                  elevation: 0,
                  margin: EdgeInsets.zero,
                ),
                inputDecorationTheme: InputDecorationTheme(
                  filled: true,
                  fillColor: const Color(0xFF090D16),
                  labelStyle: const TextStyle(color: Color(0xFF94A3B8), fontSize: 13, fontWeight: FontWeight.w600),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: BorderSide(color: Colors.white.withOpacity(0.04)),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: BorderSide(color: Colors.white.withOpacity(0.04)),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: const BorderSide(color: Color(0xFF6366F1), width: 2),
                  ),
                ),
              ),
              home: BlocBuilder<AuthBloc, AuthState>(
                builder: (context, state) {
                  if (state is AuthSuccess) {
                    context.read<HmsBloc>().add(HmsFetchData());
                    return const DashboardPage();
                  }
                  return const LoginPage();
                },
              ),
            );
          },
        );
      },
    );
  }
}
