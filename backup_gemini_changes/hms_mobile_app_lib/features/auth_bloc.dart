import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../core/api_service.dart';

// -----------------------------
// Auth Events
// -----------------------------
abstract class AuthEvent extends Equatable {
  const AuthEvent();
  @override
  List<Object?> get props => [];
}

class AuthLoginRequested extends AuthEvent {
  final String url;
  final String clientId;
  final String clientSecret;
  final String username;
  final String password;

  const AuthLoginRequested({
    required this.url,
    required this.clientId,
    required this.clientSecret,
    required this.username,
    required this.password,
  });

  @override
  List<Object?> get props => [url, clientId, clientSecret, username, password];
}

class AuthLogoutRequested extends AuthEvent {}

// -----------------------------
// Auth States
// -----------------------------
abstract class AuthState extends Equatable {
  const AuthState();
  @override
  List<Object?> get props => [];
}

class AuthInitial extends AuthState {}
class AuthLoading extends AuthState {}
class AuthSuccess extends AuthState {}
class AuthFailure extends AuthState {
  final String errorMessage;
  const AuthFailure(this.errorMessage);
  @override
  List<Object?> get props => [errorMessage];
}

// -----------------------------
// Auth Bloc
// -----------------------------
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final ApiService apiService;

  AuthBloc(this.apiService) : super(AuthInitial()) {
    on<AuthLoginRequested>((event, emit) async {
      emit(AuthLoading());
      try {
        apiService.configure(event.url, event.clientId, event.clientSecret);
        final success = await apiService.login(event.username, event.password);
        if (success) {
          emit(AuthSuccess());
        } else {
          emit(const AuthFailure("Authentication failed. Invalid credentials or unreachable server."));
        }
      } catch (e) {
        emit(AuthFailure(e.toString()));
      }
    });

    on<AuthLogoutRequested>((event, emit) {
      apiService.logout();
      emit(AuthInitial());
    });
  }
}
