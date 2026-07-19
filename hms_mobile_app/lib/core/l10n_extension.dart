import 'package:hms_mobile_app/l10n/app_localizations.dart';

extension AppLocalizationsHelper on AppLocalizations {
  String get welcomeBack {
    switch (localeName) {
      case 'ta':
        return "வரவேற்கிறோம்";
      case 'si':
        return "සාදරයෙන් පිළිගනිමු";
      default:
        return "Welcome Back";
    }
  }

  String get password {
    switch (localeName) {
      case 'ta':
        return "கடவுச்சொல்";
      case 'si':
        return "මුරපදය";
      default:
        return "Password";
    }
  }

  String get login {
    switch (localeName) {
      case 'ta':
        return "உள்நுழை";
      case 'si':
        return "ඇතුල් වන්න";
      default:
        return "Login";
    }
  }

  String get allTime {
    switch (localeName) {
      case 'ta':
        return "எல்லா நேரமும்";
      case 'si':
        return "සෑම විටම";
      default:
        return "All Time";
    }
  }

  String get today {
    switch (localeName) {
      case 'ta':
        return "இன்று";
      case 'si':
        return "අද";
      default:
        return "Today";
    }
  }

  String get yesterday {
    switch (localeName) {
      case 'ta':
        return "நேற்று";
      case 'si':
        return "ඊයේ";
      default:
        return "Yesterday";
    }
  }

  String get last7Days {
    switch (localeName) {
      case 'ta':
        return "கடந்த 7 நாட்கள்";
      case 'si':
        return "පසුගිය දින 7";
      default:
        return "7 Days";
    }
  }

  String get last30Days {
    switch (localeName) {
      case 'ta':
        return "கடந்த 30 நாட்கள்";
      case 'si':
        return "පසුගිය දින 30";
      default:
        return "30 Days";
    }
  }
}
