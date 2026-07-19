import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../core/api_service.dart';

// -----------------------------
// HMS Events
// -----------------------------
abstract class HmsEvent extends Equatable {
  const HmsEvent();
  @override
  List<Object?> get props => [];
}

class HmsFetchData extends HmsEvent {}

// -----------------------------
// HMS States
// -----------------------------
abstract class HmsState extends Equatable {
  const HmsState();
  @override
  List<Object?> get props => [];
}

class HmsInitial extends HmsState {}
class HmsLoading extends HmsState {}
class HmsSuccess extends HmsState {
  final List<dynamic> sales;
  final List<dynamic> bookings;
  final List<dynamic> rooms;
  final Map<String, dynamic> profitLoss;
  final Map<String, dynamic> userProfile;
  final List<dynamic> expenses;
  final bool isHmsEnabled;

  const HmsSuccess({
    required this.sales,
    required this.bookings,
    required this.rooms,
    required this.profitLoss,
    required this.userProfile,
    required this.expenses,
    required this.isHmsEnabled,
  });

  @override
  List<Object?> get props => [sales, bookings, rooms, profitLoss, userProfile, expenses, isHmsEnabled];
}
class HmsFailure extends HmsState {
  final String errorMessage;
  const HmsFailure(this.errorMessage);
  @override
  List<Object?> get props => [errorMessage];
}

// -----------------------------
// HMS Bloc
// -----------------------------
class HmsBloc extends Bloc<HmsEvent, HmsState> {
  final ApiService apiService;

  HmsBloc(this.apiService) : super(HmsInitial()) {
    on<HmsFetchData>((event, emit) async {
      emit(HmsLoading());
      try {
        // Fetch all data in parallel with safe error fallbacks
        final results = await Future.wait([
          apiService.fetchSales().catchError((e) {
            print("Safe fallback sales error: $e");
            return <dynamic>[];
          }),
          apiService.fetchBookings().catchError((e) {
            print("Safe fallback bookings error: $e");
            return <dynamic>[];
          }),
          apiService.fetchRooms().catchError((e) {
            print("Safe fallback rooms error: $e");
            return <dynamic>[];
          }),
          apiService.fetchProfitLoss().catchError((e) {
            print("Safe fallback profitLoss error: $e");
            return <String, dynamic>{};
          }),
          apiService.fetchUserProfile().catchError((e) {
            print("Safe fallback userProfile error: $e");
            return <String, dynamic>{};
          }),
          apiService.fetchExpenses().catchError((e) {
            print("Safe fallback expenses error: $e");
            return <dynamic>[];
          }),
          apiService.fetchBusinessDetails().catchError((e) {
            print("Safe fallback businessDetails error: $e");
            return <String, dynamic>{};
          }),
        ]);

        final sales = (results[0] as List<dynamic>?) ?? [];
        final bookings = (results[1] as List<dynamic>?) ?? [];
        final rooms = (results[2] as List<dynamic>?) ?? [];
        final profitLoss = (results[3] as Map<String, dynamic>?) ?? {};
        final userProfile = (results[4] as Map<String, dynamic>?) ?? {};
        final expenses = (results[5] as List<dynamic>?) ?? [];
        final businessDetails = (results[6] as Map<String, dynamic>?) ?? {};

        final enabledModules = businessDetails['enabled_modules'] as List<dynamic>? ?? [];
        final isHmsEnabled = enabledModules.contains('hms_module') || bookings.isNotEmpty || rooms.isNotEmpty;

        emit(HmsSuccess(
          sales: sales,
          bookings: bookings,
          rooms: rooms,
          profitLoss: profitLoss,
          userProfile: userProfile,
          expenses: expenses,
          isHmsEnabled: isHmsEnabled,
        ));
      } catch (e) {
        emit(HmsFailure("Failed to load HMS data: ${e.toString()}"));
      }
    });
  }
}
