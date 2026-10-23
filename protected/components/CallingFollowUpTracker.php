<?php

/**
 * Derives calling/follow-up status from bookings + call history.
 * Does not rewrite due dates. Never-called is today-only.
 */
class CallingFollowUpTracker
{
	const NEVER_CALLED = 'never_called';
	const FIRST_CALL = 'first_call';
	const UPCOMING = 'upcoming';
	const DUE = 'due';
	const ON_TIME = 'on_time';
	const MISSED = 'missed';
	const LATE = 'late';
	const STILL_MISSED = 'still_missed';

	protected $phaseId;
	protected $today;
	protected $bookings = array();
	protected $neverCalled = array();
	protected $firstCalls = array();
	protected $followUps = array();

	public function __construct($phaseId, $today = null)
	{
		$this->phaseId = $phaseId;
		$this->today = $today ? $today : date('Y-m-d');
		$this->load();
	}

	public static function statusMeta()
	{
		return array(
			self::FIRST_CALL => array(
				'label' => 'First Call',
				'short' => 'First Calls',
				'color' => '#5bc0de',
				'textColor' => '#ffffff',
			),
			self::DUE => array(
				'label' => 'Due Follow-up',
				'short' => 'Due Follow-ups',
				'color' => '#f0ad4e',
				'textColor' => '#ffffff',
			),
			self::ON_TIME => array(
				'label' => 'On-Time Follow-up',
				'short' => 'On-Time Follow-ups',
				'color' => '#5cb85c',
				'textColor' => '#ffffff',
			),
			self::LATE => array(
				'label' => 'Late Follow-up',
				'short' => 'Late Follow-ups',
				'color' => '#337ab7',
				'textColor' => '#ffffff',
			),
			self::MISSED => array(
				'label' => 'Missed Follow-up',
				'short' => 'Missed Follow-ups',
				'color' => '#d9534f',
				'textColor' => '#ffffff',
			),
			self::STILL_MISSED => array(
				'label' => 'Still Missed Follow-up',
				'short' => 'Still Missed',
				'color' => '#c9302c',
				'textColor' => '#ffffff',
			),
			self::UPCOMING => array(
				'label' => 'Upcoming Follow-up',
				'short' => 'Upcoming Follow-ups',
				'color' => '#9e9e9e',
				'textColor' => '#ffffff',
			),
			self::NEVER_CALLED => array(
				'label' => 'Never Called',
				'short' => 'Never Called',
				'color' => '#eeeeee',
				'textColor' => '#333333',
				'borderColor' => '#bdbdbd',
			),
		);
	}

	public static function followupLabel($number)
	{
		$number = intval($number);
		$names = array(
			1 => 'First Follow-up',
			2 => 'Second Follow-up',
			3 => 'Third Follow-up',
			4 => 'Fourth Follow-up',
			5 => 'Fifth Follow-up',
		);
		if (isset($names[$number])) {
			return $names[$number];
		}
		if ($number < 1) {
			return 'Follow-up';
		}
		$mod100 = $number % 100;
		$mod10 = $number % 10;
		if ($mod100 >= 11 && $mod100 <= 13) {
			$suffix = 'th';
		} elseif ($mod10 === 1) {
			$suffix = 'st';
		} elseif ($mod10 === 2) {
			$suffix = 'nd';
		} elseif ($mod10 === 3) {
			$suffix = 'rd';
		} else {
			$suffix = 'th';
		}
		return $number.$suffix.' Follow-up';
	}

	public function getToday()
	{
		return $this->today;
	}

	public function calendarEvents($start, $end)
	{
		$counts = array();
		$endExclusive = $end;

		foreach ($this->firstCalls as $item) {
			$dt = $item['call_date'];
			if ($dt >= $start && $dt < $endExclusive) {
				$this->bumpCount($counts, $dt, self::FIRST_CALL, 0);
			}
		}

		foreach ($this->followUps as $item) {
			$due = $item['due_followup_date'];
			$actual = $item['actual_followup_date'];
			$n = $item['followup_number'];
			$status = $item['followup_status'];

			if ($due >= $start && $due < $endExclusive) {
				if ($status === self::LATE) {
					$this->bumpCount($counts, $due, self::LATE, $n);
				} elseif ($status === self::DUE) {
					$this->bumpCount($counts, $due, self::DUE, $n);
				} elseif ($status === self::UPCOMING) {
					$this->bumpCount($counts, $due, self::UPCOMING, $n);
				} elseif ($status === self::MISSED) {
					$this->bumpCount($counts, $due, self::MISSED, $n);
				} elseif ($status === self::ON_TIME && $actual === $due) {
					$this->bumpCount($counts, $due, self::ON_TIME, $n);
				}
			}

			if ($actual && $actual !== $due && $actual >= $start && $actual < $endExclusive) {
				if ($status === self::LATE) {
					$this->bumpCount($counts, $actual, self::LATE, $n);
				} elseif ($status === self::ON_TIME) {
					$this->bumpCount($counts, $actual, self::ON_TIME, $n);
				}
			}

			if ($status === self::MISSED && $this->today > $due && $this->today >= $start && $this->today < $endExclusive) {
				$this->bumpCount($counts, $this->today, self::STILL_MISSED, $n);
			}
		}

		if ($this->today >= $start && $this->today < $endExclusive && $this->neverCalled) {
			$counts[$this->today][self::NEVER_CALLED] = array(
				'count' => count($this->neverCalled),
				'numbers' => array(),
			);
		}

		$meta = self::statusMeta();
		$events = array();
		foreach ($counts as $dt => $types) {
			foreach ($types as $type => $bucket) {
				if (!isset($meta[$type]) || $bucket['count'] < 1) {
					continue;
				}
				$events[] = array(
					'title' => $this->eventTitle($type, $bucket),
					'start' => $dt,
					'allDay' => true,
					'color' => $meta[$type]['color'],
					'textColor' => $meta[$type]['textColor'],
					'borderColor' => isset($meta[$type]['borderColor']) ? $meta[$type]['borderColor'] : $meta[$type]['color'],
					'type' => $type,
					'extendedProps' => array('type' => $type),
				);
			}
		}
		return $events;
	}

	public function summary($date)
	{
		$meta = self::statusMeta();
		$counts = array(
			self::FIRST_CALL => 0,
			self::DUE => 0,
			self::MISSED => 0,
			self::LATE => 0,
			self::ON_TIME => 0,
			self::NEVER_CALLED => 0,
			self::UPCOMING => 0,
		);

		if ($date === $this->today) {
			foreach ($this->firstCalls as $item) {
				if ($item['call_date'] === $date) {
					$counts[self::FIRST_CALL]++;
				}
			}
			foreach ($this->followUps as $item) {
				$due = $item['due_followup_date'];
				$actual = $item['actual_followup_date'];
				$status = $item['followup_status'];
				if ($status === self::DUE) {
					$counts[self::DUE]++;
				} elseif ($status === self::MISSED) {
					$counts[self::MISSED]++;
				} elseif ($status === self::LATE) {
					$counts[self::LATE]++;
				} elseif ($status === self::ON_TIME) {
					$counts[self::ON_TIME]++;
				} elseif ($status === self::UPCOMING) {
					$counts[self::UPCOMING]++;
				}
			}
			$counts[self::NEVER_CALLED] = count($this->neverCalled);
		} else {
			$groups = $this->dayItems($date, 'all');
			foreach ($groups as $type => $rows) {
				$n = count($rows);
				if ($type === self::STILL_MISSED) {
					$counts[self::MISSED] += $n;
				} elseif (isset($counts[$type])) {
					$counts[$type] += $n;
				}
			}
		}

		$out = array();
		$order = array(self::FIRST_CALL, self::DUE, self::MISSED, self::LATE, self::ON_TIME, self::NEVER_CALLED, self::UPCOMING);
		foreach ($order as $type) {
			$out[] = array(
				'type' => $type,
				'label' => $meta[$type]['short'],
				'count' => $counts[$type],
				'color' => $meta[$type]['color'],
				'textColor' => $meta[$type]['textColor'],
			);
		}
		return array(
			'date' => $date,
			'today' => $this->today,
			'isToday' => ($date === $this->today),
			'items' => $out,
		);
	}

	public function dayItems($date, $type = null)
	{
		$items = array();

		if ($type === null || $type === 'all' || $type === self::NEVER_CALLED) {
			if ($date === $this->today) {
				foreach ($this->neverCalled as $item) {
					$items[self::NEVER_CALLED][] = $item;
				}
			}
		}

		if ($type === null || $type === 'all' || $type === self::FIRST_CALL) {
			foreach ($this->firstCalls as $item) {
				if ($item['call_date'] === $date) {
					$items[self::FIRST_CALL][] = $item;
				}
			}
		}

		foreach ($this->followUps as $item) {
			$due = $item['due_followup_date'];
			$actual = $item['actual_followup_date'];
			$status = $item['followup_status'];
			$appear = array();

			if ($due === $date) {
				if (!($status === self::ON_TIME && $actual !== $due)) {
					$appear[] = $status;
				}
			}
			if ($actual && $actual !== $due && $actual === $date && ($status === self::LATE || $status === self::ON_TIME)) {
				$appear[] = $status;
			}
			if ($status === self::MISSED && $date === $this->today && $date > $due) {
				$appear[] = self::STILL_MISSED;
			}

			$appear = array_unique($appear);
			foreach ($appear as $appearType) {
				if ($type !== null && $type !== 'all' && $type !== $appearType) {
					continue;
				}
				$row = $item;
				$row['calendar_status'] = $appearType;
				$items[$appearType][] = $row;
			}
		}

		$order = array(self::FIRST_CALL, self::DUE, self::ON_TIME, self::LATE, self::MISSED, self::STILL_MISSED, self::UPCOMING, self::NEVER_CALLED);
		$grouped = array();
		foreach ($order as $key) {
			if (!empty($items[$key])) {
				$grouped[$key] = $items[$key];
			} elseif ($type === $key) {
				$grouped[$key] = array();
			}
		}
		return $grouped;
	}

	public function timeline($bookingId)
	{
		$bookingId = intval($bookingId);
		$booking = isset($this->bookings[$bookingId]) ? $this->bookings[$bookingId] : null;
		$entries = array();

		if (!$booking) {
			return array('booking' => null, 'entries' => array(), 'neverCalled' => false);
		}

		if (isset($this->neverCalled[$bookingId])) {
			return array(
				'booking' => $booking,
				'entries' => array(),
				'neverCalled' => true,
			);
		}

		foreach ($this->firstCalls as $item) {
			if ($item['booking_id'] == $bookingId) {
				$entries[] = array(
					'date' => $item['call_date'],
					'status' => self::FIRST_CALL,
					'title' => 'First Call',
					'item' => $item,
					'sort' => $item['call_date'].'-00-0',
				);
			}
		}

		foreach ($this->followUps as $item) {
			if ($item['booking_id'] != $bookingId) {
				continue;
			}
			$label = self::followupLabel($item['followup_number']);
			$n = intval($item['followup_number']);
			if (!$item['actual_followup_date']) {
				$entries[] = array(
					'date' => $item['due_followup_date'],
					'status' => $item['followup_status'],
					'title' => $label,
					'item' => $item,
					'sort' => $item['due_followup_date'].sprintf('-%02d-1', $n),
				);
			} elseif ($item['followup_status'] === self::LATE) {
				$entries[] = array(
					'date' => $item['due_followup_date'],
					'status' => self::LATE,
					'title' => $label,
					'item' => $item,
					'sort' => $item['due_followup_date'].sprintf('-%02d-1', $n),
				);
				$entries[] = array(
					'date' => $item['actual_followup_date'],
					'status' => self::LATE,
					'title' => $label.' Completed Late',
					'item' => $item,
					'sort' => $item['actual_followup_date'].sprintf('-%02d-2', $n),
				);
			} else {
				$entries[] = array(
					'date' => $item['actual_followup_date'],
					'status' => $item['followup_status'],
					'title' => $label,
					'item' => $item,
					'sort' => $item['actual_followup_date'].sprintf('-%02d-1', $n),
				);
			}
		}

		usort($entries, array($this, 'sortTimeline'));
		return array(
			'booking' => $booking,
			'entries' => $entries,
			'neverCalled' => false,
		);
	}

	protected function sortTimeline($a, $b)
	{
		$ak = isset($a['sort']) ? $a['sort'] : $a['date'].'-0';
		$bk = isset($b['sort']) ? $b['sort'] : $b['date'].'-0';
		if ($ak === $bk) {
			return 0;
		}
		return ($ak < $bk) ? -1 : 1;
	}

	protected function load()
	{
		$bookingRows = Yii::app()->db->createCommand()
			->select('cp.id, cp.customer_id, cp.createdOn, c.name as customer_name, c.mobile, p.block_number, p.plot_type, p.plot_number')
			->from('customer_plots cp')
			->join('customers c', 'c.id = cp.customer_id')
			->join('plots p', 'p.id = cp.plot_id')
			->where('cp.phase_id = :phase AND cp.status = 1 AND cp.blocked = 0', array(
				':phase' => $this->phaseId,
			))
			->order('p.block_number ASC, p.plot_number ASC')
			->queryAll();

		foreach ($bookingRows as $row) {
			$row['plot_label'] = '*'.$row['block_number'].'-'.$row['plot_type'].'-'.$row['plot_number'].'*';
			$this->bookings[intval($row['id'])] = $row;
		}

		$callRows = Yii::app()->db->createCommand()
			->select('t.id, t.booking_id, t.created_at, t.user_id, t.remarks, t.booking_status, t.call_status, t.next_reminder, t.call_audio_file, u.first_name as called_by')
			->from('booking_call_history t')
			->join('customer_plots cp', 'cp.id = t.booking_id')
			->leftJoin('users u', 'u.id = t.user_id')
			->where('cp.phase_id = :phase AND cp.status = 1 AND cp.blocked = 0', array(
				':phase' => $this->phaseId,
			))
			->order('t.booking_id ASC, t.id ASC')
			->queryAll();

		$callsByBooking = array();
		foreach ($callRows as $call) {
			$callsByBooking[intval($call['booking_id'])][] = $call;
		}

		foreach ($this->bookings as $bookingId => $booking) {
			if (empty($callsByBooking[$bookingId])) {
				$this->neverCalled[$bookingId] = $this->makeNeverCalledItem($booking);
				continue;
			}
			$this->buildBookingCycles($booking, $callsByBooking[$bookingId]);
		}
	}

	protected function buildBookingCycles($booking, $calls)
	{
		$followupNumber = 0;
		$pendingDue = null;
		$pendingScheduledBy = null;

		foreach ($calls as $index => $call) {
			$callDate = $this->asDate($call['created_at']);
			$nextDue = $this->validDate($call['next_reminder']);

			if ($index === 0) {
				$this->firstCalls[] = $this->makeFirstCallItem($booking, $call, $callDate, $nextDue);
			} elseif ($pendingDue) {
				$followupNumber++;
				$this->followUps[] = $this->makeFollowUpItem(
					$booking,
					$followupNumber,
					$pendingDue,
					$callDate,
					$pendingScheduledBy,
					$call,
					$nextDue
				);
			}

			$pendingDue = $nextDue;
			$pendingScheduledBy = $call;
		}

		if ($pendingDue) {
			$followupNumber++;
			$this->followUps[] = $this->makeFollowUpItem(
				$booking,
				$followupNumber,
				$pendingDue,
				null,
				$pendingScheduledBy,
				null,
				null
			);
		}
	}

	protected function pendingStatus($due)
	{
		if ($this->today < $due) {
			return self::UPCOMING;
		}
		if ($this->today === $due) {
			return self::DUE;
		}
		return self::MISSED;
	}

	protected function completedStatus($due, $actual)
	{
		if ($actual > $due) {
			return self::LATE;
		}
		return self::ON_TIME;
	}

	protected function makeNeverCalledItem($booking)
	{
		return array(
			'booking_id' => intval($booking['id']),
			'customer_id' => intval($booking['customer_id']),
			'customer_name' => $booking['customer_name'],
			'mobile' => $booking['mobile'],
			'plot_label' => $booking['plot_label'],
			'booked_on' => $this->asDate($booking['createdOn']),
			'call_id' => null,
			'call_date' => null,
			'call_type' => self::NEVER_CALLED,
			'followup_number' => 0,
			'followup_label' => 'Never Called',
			'due_followup_date' => null,
			'actual_followup_date' => null,
			'followup_status' => self::NEVER_CALLED,
			'called_by' => null,
			'call_status' => null,
			'booking_call_status' => null,
			'remarks' => null,
			'next_reminder' => null,
			'call_audio_file' => null,
		);
	}

	protected function makeFirstCallItem($booking, $call, $callDate, $nextDue)
	{
		return array(
			'booking_id' => intval($booking['id']),
			'customer_id' => intval($booking['customer_id']),
			'customer_name' => $booking['customer_name'],
			'mobile' => $booking['mobile'],
			'plot_label' => $booking['plot_label'],
			'booked_on' => $this->asDate($booking['createdOn']),
			'call_id' => intval($call['id']),
			'call_date' => $callDate,
			'call_type' => self::FIRST_CALL,
			'followup_number' => 0,
			'followup_label' => 'First Call',
			'due_followup_date' => null,
			'actual_followup_date' => $callDate,
			'followup_status' => self::FIRST_CALL,
			'called_by' => $call['called_by'],
			'call_status' => $call['call_status'],
			'booking_call_status' => $call['booking_status'],
			'remarks' => $call['remarks'],
			'next_reminder' => $nextDue,
			'call_audio_file' => $call['call_audio_file'],
		);
	}

	protected function makeFollowUpItem($booking, $number, $due, $actual, $scheduledBy, $completedBy, $nextDue)
	{
		$status = $actual ? $this->completedStatus($due, $actual) : $this->pendingStatus($due);
		$call = $completedBy ? $completedBy : $scheduledBy;
		$callType = self::followUpCallType($status);

		return array(
			'booking_id' => intval($booking['id']),
			'customer_id' => intval($booking['customer_id']),
			'customer_name' => $booking['customer_name'],
			'mobile' => $booking['mobile'],
			'plot_label' => $booking['plot_label'],
			'booked_on' => $this->asDate($booking['createdOn']),
			'call_id' => $call ? intval($call['id']) : null,
			'call_date' => $actual,
			'call_type' => $callType,
			'followup_number' => $number,
			'followup_label' => self::followupLabel($number),
			'due_followup_date' => $due,
			'actual_followup_date' => $actual,
			'followup_status' => $status,
			'called_by' => $call ? $call['called_by'] : null,
			'call_status' => $call ? $call['call_status'] : null,
			'booking_call_status' => $call ? $call['booking_status'] : null,
			'remarks' => $call ? $call['remarks'] : null,
			'next_reminder' => $nextDue,
			'call_audio_file' => $call ? $call['call_audio_file'] : null,
		);
	}

	protected static function followUpCallType($status)
	{
		if ($status === self::LATE) {
			return 'late_follow_up';
		}
		if ($status === self::FIRST_CALL) {
			return 'first_call';
		}
		return 'follow_up';
	}

	protected function bumpCount(&$counts, $date, $type, $number)
	{
		if (!isset($counts[$date][$type])) {
			$counts[$date][$type] = array('count' => 0, 'numbers' => array());
		}
		$counts[$date][$type]['count']++;
		if ($number > 0) {
			$counts[$date][$type]['numbers'][$number] = $number;
		}
	}

	protected function eventTitle($type, $bucket)
	{
		$meta = self::statusMeta();
		$count = intval($bucket['count']);
		$label = $meta[$type]['label'];
		if ($type === self::NEVER_CALLED || $type === self::FIRST_CALL) {
			return $count.' '.$label;
		}
		$numbers = array_values($bucket['numbers']);
		if (count($numbers) === 1) {
			$seq = self::followupLabel($numbers[0]);
			if ($type === self::DUE) {
				return $count.' '.$seq.' Due';
			}
			if ($type === self::MISSED || $type === self::STILL_MISSED) {
				$prefix = ($type === self::STILL_MISSED) ? 'Still Missed ' : 'Missed ';
				return $count.' '.$prefix.$seq;
			}
			if ($type === self::LATE) {
				return $count.' Late '.$seq;
			}
			if ($type === self::ON_TIME) {
				return $count.' On-Time '.$seq;
			}
			if ($type === self::UPCOMING) {
				return $count.' Upcoming '.$seq;
			}
		}
		return $count.' '.$label;
	}

	protected function asDate($value)
	{
		if (!$this->validDate($value)) {
			return null;
		}
		return date('Y-m-d', strtotime($value));
	}

	protected function validDate($value)
	{
		if ($value === null || $value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
			return null;
		}
		$ts = strtotime($value);
		if ($ts === false) {
			return null;
		}
		$formatted = date('Y-m-d', $ts);
		if ($formatted === '1970-01-01' || $formatted === '0000-00-00') {
			return null;
		}
		return $formatted;
	}
}
