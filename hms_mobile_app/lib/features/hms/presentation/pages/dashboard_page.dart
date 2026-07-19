import 'dart:async';
import 'package:flutter/material.dart';
import 'package:hms_mobile_app/core/app_config.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:hms_mobile_app/l10n/app_localizations.dart';
import 'package:hms_mobile_app/features/hms_bloc.dart';
import '../widgets/shared_widgets.dart';
import 'package:hms_mobile_app/core/l10n_extension.dart';
import 'overview_page.dart';
import 'bookings_page.dart';
import 'rooms_page.dart';
import 'sales_page.dart';
import 'profile_page.dart';

class DashboardPage extends StatefulWidget {
  const DashboardPage({Key? key}) : super(key: key);

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  int _currentIndex = 0;
  String _selectedDateFilter = 'Today'; // options: 'All Time', 'Today', 'Yesterday', '7 Days', '30 Days', 'Custom'
  DateTimeRange? _customDateRange;

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  Future<void> _selectCustomDateRange(BuildContext context) async {
    final DateTimeRange? picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      initialDateRange: _customDateRange ?? DateTimeRange(
        start: DateTime.now().subtract(const Duration(days: 7)),
        end: DateTime.now(),
      ),
      builder: (context, child) {
        return Theme(
          data: ThemeData.dark().copyWith(
            colorScheme: const ColorScheme.dark(
              primary: Color(0xFF6366F1),
              onPrimary: Colors.white,
              surface: Color(0xFF131A26),
              onSurface: Colors.white,
            ),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() {
        _customDateRange = picked;
        _selectedDateFilter = 'Custom';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    return Scaffold(
      appBar: AppBar(
        title: BlocBuilder<HmsBloc, HmsState>(
          builder: (context, state) {
            final isHmsEnabled = state is HmsSuccess ? state.isHmsEnabled : true;
            return Row(
              children: [
                Icon(
                  isHmsEnabled ? Icons.apartment : Icons.storefront,
                  color: const Color(0xFF6366F1),
                ),
                const SizedBox(width: 10),
                Text(
                  isHmsEnabled ? "Mahdev ERP" : AppConfig.activeBusinessName,
                  style: TextStyle(
                    fontWeight: FontWeight.w900,
                    color: Theme.of(context).brightness == Brightness.dark ? Colors.white : Colors.black87,
                  ),
                ),
              ],
            );
          },
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Color(0xFF6366F1)),
            tooltip: l10n.refresh,
            onPressed: () {
              context.read<HmsBloc>().add(HmsFetchData());
            },
          ),
        ],
        elevation: 0,
        backgroundColor: Colors.transparent,
      ),
      body: BlocBuilder<HmsBloc, HmsState>(
        builder: (context, state) {
          if (state is HmsLoading || state is HmsInitial) {
            return const ShimmerPage();
          } else if (state is HmsSuccess) {
            // Apply Date Filters on sales, bookings, and stats
            final filteredSales = state.sales.where((sale) {
              try {
                final dateStr = sale['transaction_date'];
                if (dateStr == null) return true;
                final date = DateTime.parse(dateStr);
                final today = DateTime.now();

                if (_selectedDateFilter == 'Today') {
                  return date.year == today.year && date.month == today.month && date.day == today.day;
                } else if (_selectedDateFilter == 'Week') {
                  return today.difference(date).inDays <= 7;
                } else if (_selectedDateFilter == 'Month') {
                  return today.difference(date).inDays <= 30;
                } else if (_selectedDateFilter == 'Year') {
                  return today.difference(date).inDays <= 365;
                } else if (_selectedDateFilter == 'Custom' && _customDateRange != null) {
                  return date.isAfter(_customDateRange!.start.subtract(const Duration(days: 1))) &&
                      date.isBefore(_customDateRange!.end.add(const Duration(days: 1)));
                }
              } catch (_) {}
              return true;
            }).toList();

            final filteredBookings = state.bookings.where((booking) {
              try {
                final arrivalStr = booking['hms_booking_arrival_date_time'];
                if (arrivalStr == null) return true;
                final date = DateTime.parse(arrivalStr);
                final today = DateTime.now();

                if (_selectedDateFilter == 'Today') {
                  return date.year == today.year && date.month == today.month && date.day == today.day;
                } else if (_selectedDateFilter == 'Week') {
                  return today.difference(date).inDays <= 7;
                } else if (_selectedDateFilter == 'Month') {
                  return today.difference(date).inDays <= 30;
                } else if (_selectedDateFilter == 'Year') {
                  return today.difference(date).inDays <= 365;
                } else if (_selectedDateFilter == 'Custom' && _customDateRange != null) {
                  return date.isAfter(_customDateRange!.start.subtract(const Duration(days: 1))) &&
                      date.isBefore(_customDateRange!.end.add(const Duration(days: 1)));
                }
              } catch (_) {}
              return true;
            }).toList();

            final filteredExpenses = state.expenses.where((expense) {
              try {
                final dateStr = expense['transaction_date'];
                if (dateStr == null) return true;
                final date = DateTime.parse(dateStr);
                final today = DateTime.now();

                if (_selectedDateFilter == 'Today') {
                  return date.year == today.year && date.month == today.month && date.day == today.day;
                } else if (_selectedDateFilter == 'Week') {
                  return today.difference(date).inDays <= 7;
                } else if (_selectedDateFilter == 'Month') {
                  return today.difference(date).inDays <= 30;
                } else if (_selectedDateFilter == 'Year') {
                  return today.difference(date).inDays <= 365;
                } else if (_selectedDateFilter == 'Custom' && _customDateRange != null) {
                  return date.isAfter(_customDateRange!.start.subtract(const Duration(days: 1))) &&
                      date.isBefore(_customDateRange!.end.add(const Duration(days: 1)));
                }
              } catch (_) {}
              return true;
            }).toList();

            final Map<String, dynamic> filteredStats = Map.from(state.profitLoss);
            final isHmsEnabled = state.isHmsEnabled;

            final List<Widget> pages = [];
            pages.add(OverviewPage(
              stats: filteredStats, 
              sales: filteredSales, 
              expenses: filteredExpenses,
              rooms: state.rooms, 
              bookings: filteredBookings,
              selectedDateFilter: _selectedDateFilter,
              customDateRange: _customDateRange,
              isHmsEnabled: isHmsEnabled,
            ));

            if (isHmsEnabled) {
              pages.add(BookingsPage(bookings: filteredBookings));
              pages.add(RoomsPage(rooms: state.rooms, bookings: filteredBookings));
            }

            pages.add(SalesPage(sales: filteredSales, expenses: filteredExpenses));
            pages.add(ProfilePage(profile: state.userProfile));

            return Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // 📅 HORIZONTAL CHIPS DATE FILTER BAR (Show ONLY if NOT on Profile tab)
                // 📅 HORIZONTAL CHIPS DATE FILTER BAR (Show ONLY if NOT on Profile tab)
                if (_currentIndex != (pages.length - 1))
                  Container(
                    height: 52,
                    padding: const EdgeInsets.symmetric(vertical: 8.0),
                    child: SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 16.0),
                      child: Row(
                        children: ['All Time', 'Today', 'Week', 'Month', 'Year', 'Custom'].map((filter) {
                          final isSelected = _selectedDateFilter == filter;
                          String label = filter;
                          if (filter == 'All Time') label = l10n.allTime;
                          if (filter == 'Today') label = l10n.today;
                          if (filter == 'Week') label = l10n.week;
                          if (filter == 'Month') label = l10n.month;
                          if (filter == 'Year') label = l10n.year;
                          if (filter == 'Custom') {
                            label = _customDateRange != null
                                ? "${_customDateRange!.start.day}/${_customDateRange!.start.month} - ${_customDateRange!.end.day}/${_customDateRange!.end.month}"
                                : "Custom";
                          }

                          return Padding(
                            padding: const EdgeInsets.only(right: 8.0),
                            child: ChoiceChip(
                              label: Text(
                                label,
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: isSelected ? Colors.white : const Color(0xFF94A3B8),
                                ),
                              ),
                              selected: isSelected,
                              selectedColor: const Color(0xFF6366F1),
                              backgroundColor: Theme.of(context).colorScheme.surface,
                              onSelected: (selected) {
                                if (filter == 'Custom') {
                                  _selectCustomDateRange(context);
                                } else if (selected) {
                                  setState(() {
                                    _selectedDateFilter = filter;
                                  });
                                }
                              },
                            ),
                          );
                        }).toList(),
                      ),
                    ),
                  ),
                
                // 🔄 ACTIVE TAB VIEWER (IndexedStack containing dynamic pages)
                Expanded(
                  child: IndexedStack(
                    index: _currentIndex >= pages.length ? pages.length - 1 : _currentIndex,
                    children: pages,
                  ),
                ),
              ],
            );
          } else if (state is HmsFailure) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(28.0),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.error_outline, size: 50, color: Colors.redAccent),
                    const SizedBox(height: 16),
                    Text(
                      state.errorMessage, 
                      textAlign: TextAlign.center, 
                      style: const TextStyle(fontSize: 15),
                    ),
                    const SizedBox(height: 24),
                    ElevatedButton.icon(
                      onPressed: () => context.read<HmsBloc>().add(HmsFetchData()),
                      icon: const Icon(Icons.refresh),
                      label: const Text("Retry"),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF6366F1),
                        foregroundColor: Colors.white,
                      ),
                    ),
                  ],
                ),
              ),
            );
          }
          return const Center(child: Text("Unknown error occurred."));
        },
      ),
      bottomNavigationBar: BlocBuilder<HmsBloc, HmsState>(
        builder: (context, state) {
          if (state is HmsSuccess) {
            final isHmsEnabled = state.isHmsEnabled;
            final List<BottomNavigationBarItem> navItems = [];

            navItems.add(BottomNavigationBarItem(
              icon: const Icon(Icons.dashboard_outlined),
              activeIcon: const Icon(Icons.dashboard),
              label: l10n.overview,
            ));

            if (isHmsEnabled) {
              navItems.add(BottomNavigationBarItem(
                icon: const Icon(Icons.date_range_outlined),
                activeIcon: const Icon(Icons.date_range),
                label: l10n.bookings,
              ));
              navItems.add(BottomNavigationBarItem(
                icon: const Icon(Icons.meeting_room_outlined),
                activeIcon: const Icon(Icons.meeting_room),
                label: l10n.rooms,
              ));
            }

            navItems.add(BottomNavigationBarItem(
              icon: const Icon(Icons.attach_money_outlined),
              activeIcon: const Icon(Icons.attach_money),
              label: l10n.sales,
            ));

            navItems.add(BottomNavigationBarItem(
              icon: const Icon(Icons.person_outline),
              activeIcon: const Icon(Icons.person),
              label: l10n.profile,
            ));

            final activeIndex = _currentIndex >= navItems.length ? navItems.length - 1 : _currentIndex;

            return Container(
              decoration: BoxDecoration(
                border: Border(
                  top: BorderSide(
                    color: (Theme.of(context).brightness == Brightness.dark ? Colors.white : Colors.black).withOpacity(0.04),
                    width: 1.5,
                  ),
                ),
              ),
              child: BottomNavigationBar(
                currentIndex: activeIndex,
                onTap: (index) {
                  setState(() {
                    _currentIndex = index;
                  });
                },
                type: BottomNavigationBarType.fixed,
                backgroundColor: Theme.of(context).colorScheme.surface,
                selectedItemColor: const Color(0xFF6366F1),
                unselectedItemColor: const Color(0xFF94A3B8),
                selectedLabelStyle: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold),
                unselectedLabelStyle: const TextStyle(fontSize: 10, fontWeight: FontWeight.w500),
                items: navItems,
              ),
            );
          }
          return const SizedBox.shrink();
        },
      ),
    );
  }
}
